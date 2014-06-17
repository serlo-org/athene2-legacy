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
namespace CacheInvalidator\Factory;

use CacheInvalidator\Invalidator\RepositoryStorageInvalidator;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryStorageInvalidatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator AbstractPluginManager */
        $storage      = $serviceLocator->getServiceLocator()->get('StrokerCache\Storage\CacheStorage');
        $cacheService = $serviceLocator->getServiceLocator()->get('strokerCache\Service\CacheService');
        return new RepositoryStorageInvalidator($cacheService, $storage);
    }
}
