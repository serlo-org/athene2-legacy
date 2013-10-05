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
namespace Event;

use Uuid\Entity\UuidHolder;
use User\Entity\UserInterface;
use Zend\Mvc\Controller\AbstractActionController;

class EventManager implements EventManagerInterface
{
    use \ClassResolver\ClassResolverAwareTrait,\Common\Traits\ObjectManagerAwareTrait;

    public function logEvent($uri, UserInterface $actor, UuidHolder $uuid, $object, $verb)
    {
        $className = $this->getClassResolver()->resolveClassName('Event\Entity\EventLogInterface');
        
        /* @var $log Entity\EventLogInterface */
        $log = new $className();
        
        $log->setEvent($this->findEventByRoute($uri));
        $log->setVerb($this->findVerb($verb));
        
        $log->setUuid($uuid->getUuidEntity());
        $log->setObject($object);
        $log->setActor($actor);
        
        $this->getObjectManager()->persist($log);
        return $this;
    }

    private function findEventByRoute($route)
    {
        $className = $this->getClassResolver()->resolveClassName('Event\Entity\EventInterface');
        $event = $this->getObjectManager()
            ->getRepository($className)
            ->findOneBy(array(
            'route' => $route
        ));
        if (! is_object($event)) {
            $event = new $className();
            $event->setRoute($route);
            $this->getObjectManager()->persist($event);
        }
        
        return $event;
    }

    private function findVerb($verb)
    {
        $className = $this->getClassResolver()->resolveClassName('Event\Entity\EventStringInterface');
        $string = $this->getObjectManager()
            ->getRepository($className)
            ->findOneBy(array(
            'string' => $verb
        ));
        if (! is_object($string)) {
            $string = new $className();
            $string->setString($verb);
            $this->getObjectManager()->persist($string);
        }
        
        return $string;
    }
}