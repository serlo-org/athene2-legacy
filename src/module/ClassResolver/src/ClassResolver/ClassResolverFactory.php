<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace ClassResolver;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClassResolverFactory implements FactoryInterface
{

    function createService(ServiceLocatorInterface $seviceLocator)
    {
        $config = $seviceLocator->get('config')['class_resolver'];

        $instance = new ClassResolver($config);
        $instance->setServiceLocator($seviceLocator);

        return $instance;
    }
}
