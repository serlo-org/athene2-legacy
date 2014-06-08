<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace PageSpeed;


use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Scanner\FileScanner;
use Zend\Console\Request as ConsoleRequest;
use Zend\ModuleManager\ModuleManager;

/**
 * Create a class cache of all classes used.
 */
class Module
{

    protected $knownClasses = array();

    /**
     * Generate code to cache from class reflection.
     * This is a total mess, I know. Just wanted to flesh out the logic.
     *
     * @todo Refactor into a class, clean up logic, DRY it up, maybe move
     *       some of this into Zend\Code
     * @param  ClassReflection $r
     * @return string
     */
    protected static function getCacheCode(ClassReflection $r)
    {
        $useString = '';
        $usesNames = array();
        if (count($uses = $r->getDeclaringFile()->getUses())) {
            foreach ($uses as $use) {
                $usesNames[$use['use']] = $use['as'];

                $useString .= "use {$use['use']}";

                if ($use['as']) {
                    $useString .= " as {$use['as']}";
                }

                $useString .= ";\n";
            }
        }

        $declaration = '';

        if ($r->isAbstract() && !$r->isInterface()) {
            $declaration .= 'abstract ';
        }

        if ($r->isFinal()) {
            $declaration .= 'final ';
        }

        if ($r->isInterface()) {
            $declaration .= 'interface ';
        }

        if (!$r->isInterface()) {
            $declaration .= 'class ';
        }

        $declaration .= $r->getShortName();

        $parentName = false;
        if (($parent = $r->getParentClass()) && $r->getNamespaceName()) {
            $parentName = array_key_exists($parent->getName(), $usesNames) ?
                ($usesNames[$parent->getName()] ? : $parent->getShortName()) :
                ((0 === strpos($parent->getName(), $r->getNamespaceName())) ?
                    substr($parent->getName(), strlen($r->getNamespaceName()) + 1) : '\\' . $parent->getName());
        } else {
            if ($parent && !$r->getNamespaceName()) {
                $parentName = '\\' . $parent->getName();
            }
        }

        if ($parentName) {
            $declaration .= " extends {$parentName}";
        }

        $interfaces = array_diff($r->getInterfaceNames(), $parent ? $parent->getInterfaceNames() : array());
        if (count($interfaces)) {
            foreach ($interfaces as $interface) {
                $iReflection = new ClassReflection($interface);
                $interfaces  = array_diff($interfaces, $iReflection->getInterfaceNames());
            }
            $declaration .= $r->isInterface() ? ' extends ' : ' implements ';
            $declaration .= implode(
                ', ',
                array_map(
                    function ($interface) use ($usesNames, $r) {
                        $iReflection = new ClassReflection($interface);
                        return (array_key_exists($iReflection->getName(), $usesNames) ?
                            ($usesNames[$iReflection->getName()] ? : $iReflection->getShortName()) :
                            ((0 === strpos($iReflection->getName(), $r->getNamespaceName())) ?
                                substr($iReflection->getName(), strlen($r->getNamespaceName()) + 1) :
                                '\\' . $iReflection->getName()));
                    },
                    $interfaces
                )
            );
        }

        $classContents = $r->getContents(false);
        $classFileDir  = dirname($r->getFileName());
        $classContents = trim(str_replace('__DIR__', sprintf("'%s'", $classFileDir), $classContents));

        $return = "\nnamespace " . $r->getNamespaceName(
            ) . " {\n" . $useString . $declaration . "\n" . $classContents . "\n}\n";

        return $return;
    }

    /**
     * Cache declared interfaces and classes to a single file
     *
     * @param  \Zend\Mvc\MvcEvent $e
     * @return void
     */
    public function cache($e)
    {
        $request = $e->getRequest();
        if ($request instanceof ConsoleRequest) {
            if (!($request->getParam('controller') == 'PageSpeed\Controller\PageSpeedController' && $request->getParam(
                    'action'
                ) == 'build')
            ) {
                return;
            }
        }

        if (file_exists(ZF_CLASS_CACHE)) {
            $this->reflectClassCache();
            $code = file_get_contents(ZF_CLASS_CACHE);
        } else {
            $code = "<?php\n";
        }

        $classes = array_merge(get_declared_interfaces(), get_declared_classes());
        foreach ($classes as $class) {
            // Skip non-Zend classes
            if (0 !== strpos($class, 'Zend')) {
                continue;
            }

            // Skip the autoloader factory and this class
            if (in_array($class, array('Zend\Loader\AutoloaderFactory', __CLASS__))) {
                continue;
            }

            if ($class === 'Zend\Loader\SplAutoloader') {
                continue;
            }

            // Skip any classes we already know about
            if (in_array($class, $this->knownClasses)) {
                continue;
            }
            $this->knownClasses[] = $class;

            $class = new ClassReflection($class);

            // Skip ZF2-based autoloaders
            if (in_array('Zend\Loader\SplAutoloader', $class->getInterfaceNames())) {
                continue;
            }

            // Skip internal classes or classes from extensions
            // (this shouldn't happen, as we're only caching Zend classes)
            if ($class->isInternal() || $class->getExtensionName()
            ) {
                continue;
            }

            $code .= static::getCacheCode($class);
        }

        file_put_contents(ZF_CLASS_CACHE, $code);
        // minify the file
        file_put_contents(ZF_CLASS_CACHE, php_strip_whitespace(ZF_CLASS_CACHE));
    }

    public function getAutoloaderConfig()
    {
        $autoloader = [];

        $autoloader['Zend\Loader\StandardAutoloader'] = [
            'namespaces' => [
                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
            ]
        ];

        if (file_exists(__DIR__ . '/autoload_classmap.php')) {
            return [
                'Zend\Loader\ClassMapAutoloader' => [
                    __DIR__ . '/autoload_classmap.php',
                ]
            ];

        }

        return $autoloader;
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Attach events
     *
     * @return void
     */
    public function init(ModuleManager $e)
    {
        $events = $e->getEventManager()->getSharedManager();
        $events->attach('Zend\Mvc\Application', 'finish', array($this, 'cache'));
    }

    /**
     * Determine what classes are present in the cache
     *
     * @return void
     */
    protected function reflectClassCache()
    {
        $scanner            = new FileScanner(ZF_CLASS_CACHE);
        $this->knownClasses = array_unique($scanner->getClassNames());
    }
}
 