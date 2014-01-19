<?php
/**
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @copyright 2013 by www.serlo.org
 * @license   LGPL
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
namespace Notification\Entity;

use Doctrine\ORM\Mapping as ORM;
use User\Entity\UserInterface;
use Uuid\Entity\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscription")
 */
class Subscription implements SubscriptionInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Uuid\Entity\Uuid")
     * @ORM\JoinColumn(name="uuid_id", referencedColumnName="id")
     */
    protected $object;

    /**
     * @ORM\OneToOne(targetEntity="User\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="boolean", name="notify_mailman")
     */
    protected $notifyMailman;

    /**
     * @return field_type $notifyMailman
     */
    public function getNotifyMailman()
    {
        return $this->notifyMailman;
    }

    /**
     * @param field_type $notifyMailman
     * @return self
     */
    public function setNotifyMailman($notifyMailman)
    {
        $this->notifyMailman = $notifyMailman === true;

        return $this;
    }

    /*
     * (non-PHPdoc) @see \User\Notification\Entity\SubscriptionInterface::setSubscriber()
     */
    public function setSubscriber(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /*
     * (non-PHPdoc) @see \User\Notification\Entity\SubscriptionInterface::getSubscriber()
     */
    public function getSubscriber()
    {
        return $this->user;
    }

    /*
     * (non-PHPdoc) @see \User\Notification\Entity\SubscriptionInterface::setSubscribedObject()
     */
    public function setSubscribedObject(UuidInterface $uuid)
    {
        $this->object = $uuid;

        return $this;
    }

    /*
     * (non-PHPdoc) @see \User\Notification\Entity\SubscriptionInterface::getSubscribedObject()
     */
    public function getSubscribedObject()
    {
        return $this->object;
    }
}