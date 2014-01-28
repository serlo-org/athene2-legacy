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
namespace Link\Listener;

use Common\Listener\AbstractSharedListenerAggregate;
use Zend\EventManager\Event;

class EntityManagerListener extends AbstractSharedListenerAggregate
{
    use\Entity\Manager\EntityManagerAwareTrait, \Link\Service\LinkServiceAwareTrait,
        \Entity\Options\ModuleOptionsAwareTrait;

    public function onCreate(Event $e)
    {
        /* var $entity \Entity\Entity\EntityInterface */
        $entity = $e->getParam('entity');
        $data   = $e->getParam('data');

        if (!array_key_exists('link', $data)) {
            return;
        }

        $options = $data['link'];
        $type    = $options['type'];

        if (isset($options['child'])) {
            $child       = $entity;
            $parent      = $this->getEntityManager()->getEntity($options['child']);
            $linkOptions = $this->getModuleOptions()->getType(
                $parent->getType()->getName()
            )->getComponent($type);

            $this->getLinkService()->associate($parent, $child, $linkOptions);

        } elseif (isset($options['parent'])) {

            $parent      = $entity;
            $child       = $this->getEntityManager()->getEntity($options['child']);
            $linkOptions = $this->getModuleOptions()->getType(
                $parent->getType()->getName()
            )->getComponent($type);

            $this->getLinkService()->associate($parent, $child, $linkOptions);
        }
    }

    /*
     * (non-PHPdoc) @see \Zend\EventManager\SharedListenerAggregateInterface::attachShared()
     */
    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            $this->getMonitoredClass(),
            'create',
            array(
                $this,
                'onCreate'
            ),
            2
        );
    }

    /*
     * (non-PHPdoc) @see \Common\Listener\AbstractSharedListenerAggregate::getMonitoredClass()
     */
    protected function getMonitoredClass()
    {
        return 'Entity\Manager\EntityManager';
    }
}