<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Event\Listener;

use Common\Listener\AbstractSharedListenerAggregate;
use Event\EventManagerAwareTrait;
use Event\EventManagerInterface;
use Event\Exception\RuntimeException;
use Instance\Entity\InstanceInterface;
use Instance\Manager\InstanceManagerAwareTrait;
use Instance\Manager\InstanceManagerInterface;
use User\Manager\UserManagerAwareTrait;
use User\Manager\UserManagerInterface;
use Uuid\Entity\UuidInterface;

abstract class AbstractListener extends AbstractSharedListenerAggregate
{
    use EventManagerAwareTrait, InstanceManagerAwareTrait, UserManagerAwareTrait;

    public function __construct(
        EventManagerInterface $eventManager,
        InstanceManagerInterface $instanceManager,
        UserManagerInterface $userManager
    ) {
        if (!class_exists($this->getMonitoredClass())) {
            throw new RuntimeException(sprintf(
                'The class you are trying to attach to does not exist: %s',
                $this->getMonitoredClass()
            ));
        }
        $this->eventManager    = $eventManager;
        $this->instanceManager = $instanceManager;
        $this->userManager     = $userManager;
    }

    public function logEvent($name, InstanceInterface $instance, $uuid, array $params = [])
    {
        $this->getEventManager()->logEvent($name, $instance, $uuid, $params);
    }
}
