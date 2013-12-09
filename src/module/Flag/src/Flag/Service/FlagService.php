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
namespace Flag\Service;

use Flag\Entity\FlagInterface;

class FlagService implements FlagServiceInterface
{
    use \User\Manager\UserManagerAwareTrait,\Uuid\Manager\UuidManagerAwareTrait;

    /**
     *
     * @var FlagInterface
     */
    protected $entity;

    public function getEntity()
    {
        return $this->entity;
    }

    public function getId()
    {
        return $this->getEntity()->getId();
    }

    public function getObject()
    {
        return $this->getUuidManager()->createService($this->getEntity()
            ->getObject());
    }

    public function getContent()
    {
        return $this->getEntity()->getContent();
    }

    public function getReporter()
    {
        $user = $this->getEntity()->getReporter();
        return $this->getUserManager()->getUser($user->getId());
    }

    public function getTimestamp()
    {
        return $this->getEntity()->getTimestamp();
    }

    public function getType()
    {
        return $this->getEntity()->getType();
    }

    public function setEntity(FlagInterface $entity)
    {
        $this->entity = $entity;
        return $this;
    }
}