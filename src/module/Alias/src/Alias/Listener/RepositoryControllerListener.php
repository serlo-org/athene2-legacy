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
namespace Alias\Listener;

use Zend\EventManager\Event;
use Common\Listener\AbstractSharedListenerAggregate;

class RepositoryControllerListener extends AbstractSharedListenerAggregate
{

    public function onCheckout(Event $e)
    {
        /* var $entity \Entity\Entity\EntityInterface */
        $entity = $e->getParam('entity');
        $language = $e->getParam('language');
        
        $url = $e->getTarget()
            ->url()
            ->fromRoute('entity/view', array(
            'id' => $entity->getId()
        ));
        
        $this->getAliasManager()->autoAlias('entity', $url, $entity->getUuidEntity(), $language);
    }
    
    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach($this->getMonitoredClass(), 'checkout', array(
            $this,
            'onCheckout'
        ));
        
        return $this;
    }
    
    protected function getMonitoredClass()
    {
        return 'Entity\Controller\RepositoryController';
    }
}