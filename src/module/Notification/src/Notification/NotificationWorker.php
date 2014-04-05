<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright   Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Notification;

use Notification\Entity\SubscriptionInterface;
use User\Entity\UserInterface;
use Uuid\Entity\UuidInterface;
use Zend\EventManager\EventManagerAwareTrait;

class NotificationWorker
{
    use\Common\Traits\ObjectManagerAwareTrait, \User\Manager\UserManagerAwareTrait, SubscriptionManagerAwareTrait,
        NotificationManagerAwareTrait, \ClassResolver\ClassResolverAwareTrait;
    use EventManagerAwareTrait;

    /**
     * @TODO Undirtyfy
     */
    public function run()
    {
        // Solves "mysql server has gone away" error for large sets of data
        $timeout        = 60 * 20;
        $notifyWorkload = [];
        ini_set('mysql.connect_timeout', $timeout);
        ini_set('default_socket_timeout', $timeout);

        if (!$this->getSubscriptionManager()->hasSubscriptions()) {
            return;
        }

        /* @var $eventLog \Event\Entity\EventLogInterface */
        foreach ($this->getWorkload() as $eventLog) {
            /* @var $subscriptions SubscriptionInterface[] */
            $object        = $eventLog->getObject();
            $subscriptions = $this->getSubscriptionManager()->findSubscriptionsByUuid($object);
            $subscribed    = [];

            foreach ($subscriptions as $subscription) {
                $subscriber = $subscription->getSubscriber();
                // Don't create notifications for myself
                if ($subscriber !== $eventLog->getActor() && $eventLog->getTimestamp() > $subscription->getTimestamp()
                ) {
                    $notification = $this->getNotificationManager()->createNotification(
                        $subscriber,
                        $eventLog
                    );
                    if ($subscription->getNotifyMailman()) {
                        $id                                     = $subscriber->getId();
                        $notifyWorkload[$id]['subscriber']      = $subscriber;
                        $notifyWorkload[$id]['notifications'][] = $notification;
                    }
                    $subscribed[] = $subscriber;
                }
            }

            foreach ($eventLog->getParameters() as $parameter) {
                if ($parameter->getValue() instanceof UuidInterface) {
                    /* @var $subscribers UserInterface[] */
                    $object        = $parameter->getValue();
                    $subscriptions = $this->getSubscriptionManager()->findSubscriptionsByUuid($object);

                    foreach ($subscriptions as $subscription) {
                        $subscriber = $subscription->getSubscriber();
                        if (!in_array($subscriber, $subscribed) && $subscriber !== $eventLog->getActor(
                            ) && $eventLog->getTimestamp() > $subscription->getTimestamp()
                        ) {
                            $notification = $this->getNotificationManager()->createNotification(
                                $subscriber,
                                $eventLog
                            );
                            if ($subscription->getNotifyMailman()) {
                                $id                                     = $subscriber->getId();
                                $notifyWorkload[$id]['subscriber']      = $subscriber;
                                $notifyWorkload[$id]['notifications'][] = $notification;
                            }
                        }
                    }
                }
            }
        }

        foreach ($notifyWorkload as $data) {
            $this->getEventManager()->trigger(
                'notify',
                $this,
                ['notifications' => $data['notifications'], 'user' => $data['subscriber']]
            );
        }
    }

    /**
     * @TODO Undirtyfy
     */
    protected function getWorkload()
    {
        $offset = $this->findOffset();
        $query  = $this->getObjectManager()->createQuery(
            sprintf(
                'SELECT el FROM %s el WHERE el.id > %d ORDER BY el.id ASC',
                $this->getClassResolver()->resolveClassName('Event\Entity\EventLogInterface'),
                $offset
            )
        );

        return $query->getResult();
    }

    /**
     * @TODO Undirtyfy
     */
    private function findOffset()
    {
        $query   = $this->getObjectManager()->createQuery(
            sprintf(
                'SELECT ne FROM %s ne ORDER BY ne.eventLog DESC',
                $this->getClassResolver()->resolveClassName('Notification\Entity\NotificationEventInterface')
            )
        );
        $results = $query->getResult();
        if (count($results)) {
            /* @var $result Entity\NotificationEventInterface */
            $result = $results[0];

            return $result->getEventLog()->getId();
        }

        return 0;
    }
}