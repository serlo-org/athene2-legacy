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
namespace Link\Listener;

use Zend\EventManager\Event;
use Common\Listener\AbstractSharedListenerAggregate;

class EntityControllerListener extends AbstractSharedListenerAggregate
{
    use\Entity\Manager\EntityManagerAwareTrait,\Link\Service\LinkServiceAwareTrait;

    public function onCreate(Event $e)
    {
        /* var $entity \Entity\Entity\EntityInterface */
        $entity = $e->getParam('entity');
        $data = $e->getParam('query');
        $user = $e->getParam('user');
        $language = $e->getParam('language');
        
        $type = 'link';
        
        $options = $data[$type];
        
        if (isset($options['child'])) {
            $child = $this->getEntityManager()->getEntity($options['child']);
            
            $this->getLinkService()->associate($entity, $child, $type);
            
            $eventData = [
                'entity' => $entity,
                'child' => $child,
                'user' => $user,
                'language' => $language
            ];
        } elseif (isset($options['parent'])) {
            $parent = $this->getEntityManager()->getEntity($options['child']);
            
            $this->getLinkService()->associate($parent, $entity, $type);
            
            $eventData = [
                'entity' => $entity,
                'parent' => $parent,
                'user' => $user,
                'language' => $language
            ];
        }
        
        $e->getTarget()
            ->getEventManager()
            ->trigger('link', $this, $eventData);
    }
    
    /*
     * (non-PHPdoc) @see \Zend\EventManager\SharedListenerAggregateInterface::attachShared()
     */
    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach($this->getMonitoredClass(), 'create', array(
            $this,
            'onCreate'
        ), 2);
    }
    
    /*
     * (non-PHPdoc) @see \Common\Listener\AbstractSharedListenerAggregate::getMonitoredClass()
     */
    protected function getMonitoredClass()
    {
        return 'Entity\Controller\EntityController';
    }
}