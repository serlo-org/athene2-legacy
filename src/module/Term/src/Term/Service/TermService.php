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
namespace Term\Service;

use Core\Service\AbstractEntityFacade;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

class TermService extends AbstractEntityDecorator implements TermServiceInterface, ObjectManagerAwareInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;
    
	/* (non-PHPdoc)
     * @see \DoctrineModule\Persistence\ObjectManagerAwareInterface::getObjectManager()
     */
    public function getObjectManager ()
    {
        return $this->objectManager;
    }

	/* (non-PHPdoc)
     * @see \DoctrineModule\Persistence\ObjectManagerAwareInterface::setObjectManager()
     */
    public function setObjectManager (\Doctrine\Common\Persistence\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        return $this;
    }
}