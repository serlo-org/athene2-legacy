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
namespace Language\Factory;

use Language\Manager\LanguageManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

trait LanguageManagerFactoryTrait
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return LanguageManagerInterface
     */
    public function getLanguageManager(ServiceLocatorInterface $serviceLocator)
    {
        return $serviceLocator->get('Language\Manager\LanguageManager');
    }
}
