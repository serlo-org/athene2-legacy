<?php
/**
 * 
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author	Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license	LGPL-3.0
 * @license	http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link		https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\I18n\Translator\Translator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        // Load translator
        
        $lm = $e->getApplication()->getServiceManager()->get('Language\Manager\LanguageManager');
        $code = $lm->getRequestLanguage()->getCode();
        
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator->addTranslationFile('PhpArray', __DIR__.'/language/routes/'.$code.'.php', 'default', 'de_DE');
        $translator->setLocale($code);
        
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        

        $app      = $e->getTarget();
        $serviceManager       = $app->getServiceManager();
        
        // Route translator
        $app->getEventManager()->attach('route', array($this, 'onPreRoute'), 4);
    }
    
    public function onPreRoute($e){
        $app      = $e->getTarget();
        $serviceManager       = $app->getServiceManager();
        $serviceManager->get('router')->setTranslator($serviceManager->get('translator'));
    }

    public function getConfig()
    {
        $config = array_merge_recursive(
            include __DIR__ . '/config/module.config.php',
            //include __DIR__ . '/config/subject/module.config.php',
            include __DIR__ . '/config/taxonomy/module.config.php',
            include __DIR__ . '/config/entity/module.config.php'
        );
        return $config; 
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
