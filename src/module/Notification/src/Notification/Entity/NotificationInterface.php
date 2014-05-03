<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Notification\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Event\Entity\EventLogInterface;
use User\Entity;

interface NotificationInterface
{

    /**
     * @param bool $seen
     * @return self
     */
    public function setSeen($seen);

    /**
     * @return bool
     */
    public function getSeen();

    /**
     * @return string
     */
    public function getEventName();

    /**
     * @return Entity\User
     */
    public function getUser();

    /**
     * @param Entity\UserInterface $user
     * @return self
     */
    public function setUser(Entity\UserInterface $user);

    /**
     * @return EventLogInterface[]|Collection
     */
    public function getEvents();

    /**
     * @param NotificationEventInterface $event
     * @return self
     */
    public function addEvent(NotificationEventInterface $event);

    /**
     * @return Collection
     */
    public function getActors();

    /**
     * @return Collection
     */
    public function getObjects();

    /**
     * @return Collection
     */
    public function getParameters();

    /**
     * @return DateTime $timestamp
     */
    public function getTimestamp();

    /**
     * @param DateTime $timestamp
     * @return void
     */
    public function setTimestamp(DateTime $timestamp);
}
