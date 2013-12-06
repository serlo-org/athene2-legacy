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
namespace Entity\Plugin\LearningResource\Listener;

use Zend\EventManager\Event;
use Taxonomy\Exception\TermNotFoundException;
use Metadata\Exception\DuplicateMetadata;
use Metadata\Exception\MetadataNotFoundException;
use Common\Listener\AbstractSharedListenerAggregate;
use Entity\Plugin\LearningResource\Exception\UnstatisfiedDependencyException;

class EntityTaxonomyPluginControllerListener extends AbstractSharedListenerAggregate
{
    use \Metadata\Manager\MetadataManagerAwareTrait;

    public function onAddToTerm(Event $e)
    {
        /* @var $term \Entity\Service\EntityServiceInterface */
        $entity = $e->getParam('entity');
        /* @var $term \Taxonomy\Service\TermServiceInterface */
        $term = $e->getParam('term');
        
        try {
            $entity->learningResource()->isStatisfied();
            
            $object = $entity->getEntity()->getUuidEntity();
            
            try {
                $subject = $term->findAncestorByTypeName('subject');
                try {
                    $this->getMetadataManager()->addMetadata($object, 'subject', $subject->getName());
                } catch (DuplicateMetadata $e) {}
            } catch (TermNotFoundException $e) {}
        } catch (UnstatisfiedDependencyException $e) {
            return false;
        }
    }

    public function onRemoveFromTerm(Event $e)
    {
        /* @var $term \Entity\Service\EntityServiceInterface */
        $entity = $e->getParam('entity');
        /* @var $term \Taxonomy\Service\TermServiceInterface */
        $term = $e->getParam('term');
        $object = $entity->getEntity()->getUuidEntity();
        
        try {
            $subject = $term->findAncestorByTypeName('subject');
            
            try {
                $metadata = $this->getMetadataManager()->findMetadataByObjectAndKeyAndValue($object, 'subject', $subject->getName());
                $this->getMetadataManager()->removeMetadata($metadata->getId());
            } catch (MetadataNotFoundException $e) {}
        } catch (TermNotFoundException $e) {}
    }

    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach($this->getMonitoredClass(), 'addToTerm', array(
            $this,
            'onAddToTerm'
        ));
        $this->listeners[] = $events->attach($this->getMonitoredClass(), 'removeFromTerm', array(
            $this,
            'onRemoveFromTerm'
        ));
    }

    protected function getMonitoredClass()
    {
        return 'Entity\Plugin\Taxonomy\Controller\TaxonomyController';
    }
}