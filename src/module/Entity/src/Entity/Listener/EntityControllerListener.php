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
namespace Entity\Listener;

use Entity\Result\UrlResult;
use Zend\EventManager\Event;
use Common\Listener\AbstractSharedListenerAggregate;

class EntityControllerListener extends AbstractSharedListenerAggregate
{

    public function onCreate(Event $e)
    {
        /* var $entity \Entity\Entity\EntityInterface */
        $entity = $e->getParam('entity');
        
        $result = new UrlResult();
        $result->setResult($e->getTarget()
            ->assemble(array(
            'entity' => $entity->getId()
        ), array(
            'name' => 'entity/repository/add-revision'
        )));
        
        return $result;
    }

    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach($this->getMonitoredClass(), 'create.postFlush', array(
            $this,
            'onCreate'
        ), - 1000);
    }

    protected function getMonitoredClass()
    {
        return 'Entity\Controller\EntityController';
    }
}