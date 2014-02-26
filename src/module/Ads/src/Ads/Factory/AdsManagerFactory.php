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

namespace Ads\Factory;


use Ads\Manager\AdsManager;
use ClassResolver\ClassResolverFactoryTrait;
use Common\Factory\AuthorizationServiceFactoryTrait;
use Common\Factory\EntityManagerFactoryTrait;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdsManagerFactory implements FactoryInterface
{
    use EntityManagerFactoryTrait, ClassResolverFactoryTrait, AuthorizationServiceFactoryTrait;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $authorizationService = $this->getAuthorizationService($serviceLocator);
        $entityManager        = $this->getEntityManager($serviceLocator);
        $classResolver        = $this->getClassResolver($serviceLocator);
        $attachmentManager    = $serviceLocator->get('Attachment\Manager\AttachmentManager');
        $adsManager           = new AdsManager($authorizationService, $attachmentManager, $classResolver, $entityManager);

        return $adsManager;
    }
}
 