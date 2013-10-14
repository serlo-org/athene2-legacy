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
namespace User\Notification\Listener;

use Zend\EventManager\Event;

class RepositoryPluginControllerListener extends AbstractListener
{
    /**
     *
     * @var array
     */
    protected $listeners = array();

    public function onAddRevision(Event $e)
    {
        $user = $e->getParam('user');
        $entity = $e->getParam('entity');
        $reference = NULL;
        
        foreach ($e->getParam('post') as $params) {
            if (is_array($params) && array_key_exists('subscription', $params)) {
                $param = $params['subscription'];
                if ($param['subscribe'] === '1') {
                    $user = $e->getParam('user');
                    $entity = $e->getParam('entity');
                    $notifyMailman = $param['mailman'] === '1' ? true : false;
                    $this->subscribe($user, $entity->getEntity(), $notifyMailman);
                }
            }
        }
    }
    
    /*
     * (non-PHPdoc) @see \Zend\EventManager\SharedListenerAggregateInterface::attachShared()
     */
    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('LearningResource\Plugin\Repository\Controller\RepositoryController', 'add-revision', array(
            $this,
            'onAddRevision'
        ), - 1);
    }
    
    /*
     * (non-PHPdoc) @see \Zend\EventManager\SharedListenerAggregateInterface::detachShared()
     */
    public function detachShared(\Zend\EventManager\SharedEventManagerInterface $events)
    {
        // TODO Auto-generated method stub
    }
}