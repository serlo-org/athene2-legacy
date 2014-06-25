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
namespace Search\Factory;

use Search\Adapter\SolrAdapter;
use Uuid\Factory\UuidManagerFactoryTrait;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SolrAdapterFactory implements FactoryInterface
{
    use UuidManagerFactoryTrait;

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator AbstractPluginManager */
        $serviceManager = $serviceLocator->getServiceLocator();
        $client         = $serviceManager->get('Solarium\Client');
        $normalizer     = $serviceManager->get('Normalizer\Normalizer');
        $router         = $serviceManager->get('Router');
        $translator     = $serviceManager->get('MvcTranslator');
        $uuidManager    = $this->getUuidManager($serviceManager);

        return new SolrAdapter($client, $normalizer, $router, $translator, $uuidManager);
    }
}
