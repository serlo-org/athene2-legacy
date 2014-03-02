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

use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\View\Model\ViewModel;

class UserControllerListener extends AbstractListener
{
    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach($this->getMonitoredClass(), 'register', [$this, 'onRegister'], -1);
    }

    protected function getMonitoredClass()
    {
        return 'User\Controller\UserController';
    }

    public function onRegister(Event $e)
    {
        /* @var $user \User\Entity\UserInterface */
        $user = $e->getParam('user');

        $subject = new ViewModel();
        $body    = new ViewModel([
            'user' => $user
        ]);

        $subject->setTemplate('mailman/messages/register/subject');
        $body->setTemplate('mailman/messages/register/body');

        $this->getMailman()->send(
            $user->getEmail(),
            $this->getMailman()->getDefaultSender(),
            $this->getRenderer()->render($subject),
            $this->getRenderer()->render($body)
        );
    }
}
