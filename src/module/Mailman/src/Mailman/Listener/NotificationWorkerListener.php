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
namespace Mailman\Listener;

use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Paginator\Adapter\Collection;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\View\Model\ViewModel;

class NotificationWorkerListener extends AbstractListener
{
    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach($this->getMonitoredClass(), 'notify', [$this, 'onNotify'], -1);
    }

    public function onNotify(Event $e)
    {
        /* @var $user \User\Entity\UserInterface */
        $user          = $e->getParam('user');
        $notifications = $e->getParam('notifications');

        if (!$notifications instanceof Collection) {
            $notifications = new ArrayCollection($notifications);
        }

        $subject = new ViewModel();
        $body    = new ViewModel([
            'user'          => $user,
            'notifications' => $notifications
        ]);

        $subject->setTemplate('mailman/messages/notification/subject');
        $body->setTemplate('mailman/messages/notification/body');

        $this->getMailman()->send(
            $user->getEmail(),
            $this->getMailman()->getDefaultSender(),
            $this->getRenderer()->render($subject),
            $this->getRenderer()->render($body)
        );
    }

    protected function getMonitoredClass()
    {
        return 'Notification\NotificationWorker';
    }
}