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
namespace Navigation\Factory;

use Common\Factory\EntityManagerFactoryTrait;
use Navigation\Form\ParameterForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ParameterFormFactory implements FactoryInterface
{
    use EntityManagerFactoryTrait, NavigationManagerFactoryTrait;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return ContainerForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager     = $this->getEntityManager($serviceLocator);
        $navigationManager = $this->getNavigationManager($serviceLocator);
        $form              = new ParameterForm($entityManager, $navigationManager);

        return $form;
    }
}
