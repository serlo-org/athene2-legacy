<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Contexter\Factory;

use Contexter\Router\Router;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RouterFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $config   = $serviceManager->get('config');
        $instance = new Router();
        $instance->setConfig($config['Manager\ContextManager']['router']);
        $instance->setServiceLocator($serviceManager);
        $instance->setRouter($serviceManager->get('Router'));
        $routeMatch = $serviceManager->get('Application')->getMvcEvent()->getRouteMatch();
        $routeMatch = $routeMatch ? : new RouteMatch([]);
        $instance->setRouteMatch($routeMatch);
        $instance->setObjectManager($serviceManager->get('Doctrine\ORM\EntityManager'));
        $instance->setClassResolver($serviceManager->get('ClassResolver\ClassResolver'));
        $instance->setContextManager($serviceManager->get('Contexter\Manager\ContextManager'));

        return $instance;
    }
}