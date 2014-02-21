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
namespace Event\Listener;

use Zend\EventManager\Event;

class UuidManagerListener extends AbstractListener
{

    public function onRestore(Event $e)
    {
        $object   = $e->getParam('object');
        $user     = $this->getUserManager()->getUserFromAuthenticator();
        $instance = $this->getInstanceManager()->getInstanceFromRequest();

        $this->logEvent('uuid/restore', $instance, $object);
    }

    public function onTrash(Event $e)
    {
        $object   = $e->getParam('object');
        $user     = $this->getUserManager()->getUserFromAuthenticator();
        $instance = $this->getInstanceManager()->getInstanceFromRequest();

        $this->logEvent('uuid/trash', $instance, $object);
    }

    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            $this->getMonitoredClass(),
            'trash',
            array(
                $this,
                'onTrash'
            )
        );
        $this->listeners[] = $events->attach(
            $this->getMonitoredClass(),
            'restore',
            array(
                $this,
                'onRestore'
            )
        );
    }

    protected function getMonitoredClass()
    {
        return 'Uuid\Manager\UuidManager';
    }
}