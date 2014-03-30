<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright   Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Navigation\Factory;

use Exception;
use Navigation\Provider\ContainerProviderInterface;
use Navigation\Provider\PageProviderInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface as Router;
use Zend\Mvc\Router\RouteStackInterface;
use Zend\Navigation\Exception\InvalidArgumentException;
use Zend\Navigation\Navigation;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

abstract class ProvideableNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        try {
            return parent::createService($serviceLocator);
        } catch (Exception $e) {
            var_dump(get_class($e) . ": " . $e->getMessage());

            return new Navigation([]);
        }
    }

    protected function getPages(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        if (null === $this->pages) {
            $configuration = $serviceLocator->get('Config');

            if (!isset($configuration['navigation'])) {
                throw new InvalidArgumentException('Could not find navigation configuration key');
            }

            if (!isset($configuration['navigation'][$this->getName()])) {
                throw new InvalidArgumentException(sprintf(
                    'Failed to find a navigation container by the name "%s"',
                    $this->getName()
                ));
            }

            $pages     = $this->getPagesFromConfig($configuration['navigation'][$this->getName()]);
            $container = $this->provideContainer($configuration);
            $pages     = ArrayUtils::merge($pages, $this->getPagesFromConfig($container));

            $this->pages = $this->preparePages($serviceLocator, $pages);
        }

        return $this->pages;
    }

    /**
     * Injects components
     *
     * @see \Zend\Navigation\Service\AbstractNavigationFactory::injectComponents()
     */
    protected function injectComponents(
        array $pages,
        RouteMatch $routeMatch = null,
        RouteStackInterface $router = null,
        $request = null
    ) {
        foreach ($pages as &$page) {
            $hasMvc = isset($page['action']) || isset($page['controller']) || isset($page['route']);
            if ($hasMvc) {
                if (!isset($page['routeMatch']) && $routeMatch) {
                    $page['routeMatch'] = $routeMatch;
                }
                if (!isset($page['router'])) {
                    $page['router'] = $router;
                }
            }

            if (isset($page['pages']) && is_array($page['pages'])) {
                $page['pages'] = $this->injectComponents($page['pages'], $routeMatch, $router, $request);
            }

            if (isset($page['provider'])) {
                $options = [];

                if (isset($page['options'])) {
                    $options = $page['options'];
                    unset($page['options']);
                }

                $className = $page['provider'];
                $provider  = $this->serviceLocator->get($className);

                try {
                    $pagesFromProvider = $this->injectComponentsFromProvider(
                        $provider,
                        $routeMatch,
                        $router,
                        (array)$options
                    );

                    if (isset($page['pages'])) {
                        $page['pages'] = ArrayUtils::merge(
                            $page['pages'],
                            $pagesFromProvider
                        );
                    } else {
                        $page['pages'] = $pagesFromProvider;
                    }
                } catch (Exception $e) {
                    var_dump($e->getMessage());
                }

                unset($page['provider']);
            }
        }

        return $pages;
    }

    protected function provideContainer(array $configuration)
    {
        $providers  = $configuration['navigation']['providers'];
        $containers = [];

        foreach ($providers as $provider) {
            $provider = $this->serviceLocator->get($provider);
            if (!$provider instanceof ContainerProviderInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Expected %s but got %s',
                    is_object($provider) ? get_class($provider) : gettype($provider)
                ));
            }
            $providedConfig = $provider->provide($this->getName());
            $containers     = ArrayUtils::merge($containers, $providedConfig);
        }

        return $containers;
    }



    protected function injectComponentsFromProvider(
        PageProviderInterface $provider,
        RouteMatch $routeMatch = null,
        Router $router = null,
        array $options = []
    ) {
        $pages  = $provider->provide($options);
        $return = $this->injectComponents($pages, $routeMatch, $router);

        return $return;
    }
}
