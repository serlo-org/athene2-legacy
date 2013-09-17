<?php
/**
 *
 *
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license LGPL-3.0
 * @license http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Taxonomy\Manager;

use Taxonomy\Entity\TermTaxonomyEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Taxonomy\Collection\TermCollection;
use Language\Service\LanguageServiceInterface;
use Taxonomy\Exception\RuntimeException;
use Taxonomy\Exception\TermNotFoundException;
use Taxonomy\Exception\InvalidArgumentException;
use Taxonomy\Service\TermServiceInterface;

class TermManager extends AbstractManager implements TermManagerInterface
{
    use\Common\Traits\ObjectManagerAwareTrait,\Language\Service\LanguageServiceAwareTrait,\Common\Traits\EntityDelegatorTrait,\Uuid\Manager\UuidManagerAwareTrait,\Taxonomy\Manager\SharedTaxonomyManagerAwareTrait,\Term\Manager\TermManagerAwareTrait,\Common\Traits\ConfigAwareTrait;

    protected function getDefaultConfig()
    {
        return array(
            'options' => array(
                'templates' => array(
                    'update' => 'taxonomy/taxonomy/update'
                ),
                'allowed_parents' => array(),
                'allowed_links' => array(),
                'radix_enabled' => true
            )
        );
    }

    /**
     *
     * @var SharedTaxonomyManager
     */
    protected $manager;

    public function getTerm($id)
    {
        if (! is_numeric($id))
            throw new InvalidArgumentException(sprintf('Expected int but got %s', gettype($id)));
        
        if (! $this->hasInstance($id)) {
            $entity = $this->getObjectManager()->find($this->getClassResolver()
                ->resolveClassName('Taxonomy\Entity\TermTaxonomyEntityInterface'), (int) $id);
            
            if (! is_object($entity))
                throw new TermNotFoundException(sprintf('Term with id %s not found', $id));
            
            $this->addTerm($this->createService($entity));
        }
        
        return $this->getInstance($id);
    }

    public function findTermByAncestors(array $ancestors)
    {
        if (! count($ancestors))
            throw new RuntimeException('Ancestors are empty');
        
        $terms = $this->getRootTermEntities();
        foreach ($ancestors as $element) {
            if (is_string($element) && strlen($element) > 0) {
                foreach ($terms as $term) {
                    $found = false;
                    if (strtolower($term->getSlug()) == strtolower($element)) {
                        $terms = $term->getChildren();
                        $found = $term;
                        break;
                    }
                }
            }
        }
        
        if (! is_object($found))
            throw new TermNotFoundException(sprintf('Could not find term with acestors: %s', implode(',', $ancestors)));
        
        if (! $this->hasTermService($found->getId())) {
            $this->addTerm($this->createService($found));
        }
        
        return $this->getInstance($found->getId());
    }

    public function hasTermService($id)
    {
        return $this->hasInstance($id);
    }

    public function updateTerm($id, array $data)
    {}

    public function createTerm(array $data, LanguageServiceInterface $language)
    {
        $entity = $this->getClassResolver()->resolve('Taxonomy\Entity\TermTaxonomyEntityInterface');
        
        try {
            $term = $this->getTermManager()->findTermBySlug($data['term']['name'], $language);
        } catch (TermNotFoundException $e) {
            $term = $this->getTermManager()->createTerm($data['term']['name'], $language);
        }
        
        $this->getUuidManager()->injectUuid($entity);
        $entity->setTerm($term->getEntity());
        $entity->setTaxonomy($this->getEntity());
        $this->hydrateTerm($data, $entity);
        
        $this->getObjectManager()->persist($entity);
        $instance = $this->createService($entity);
        return $instance;
    }

    public function deleteTerm($term)
    {
        $id = $term->getId();
        $this->getObjectManager()->remove($term->getEntity());
        $this->removeInstance($id);
        unset($term);
        return $this;
    }

    public function addTerm(TermServiceInterface $termService)
    {
        $this->addInstance($termService->getId(), $termService);
        return $termService->getId();
    }

    public function getRootTerms()
    {
        $collection = $this->getRootTermEntities();
        return new TermCollection($collection, $this->getSharedTaxonomyManager());
    }

    protected function createService(TermTaxonomyEntityInterface $entity)
    {
        $instance = $this->createInstance('Taxonomy\Service\TermServiceInterface');
        $instance->setEntity($entity);
        if ($entity->getTaxonomy() !== $this->getEntity()) {
            $instance->setManager($this->getSharedTaxonomyManager()
                ->get($entity->getTaxonomy()->getId()));
        } else {
            $instance->setManager($this);
        }
        $this->addTerm($instance);
        return $instance;
    }

    public function getAllowedChildrenTypes()
    {
        return $this->getSharedTaxonomyManager()->getAllowedChildrenTypes($this->getEntity()
            ->getName());
    }

    public function allowsParentType($type)
    {
        return in_array($type, $this->getOption('allowed_parents'));
    }

    public function getAllowedParentTypes()
    {
        return $this->getOption('allowed_parents');
    }

    public function getId()
    {
        return $this->getEntity()->getId();
    }

    public function getType()
    {
        return $this->getEntity()->getType();
    }

    public function setType($type)
    {
        return $this->getEntity()->setType($type);
    }

    public function getTerms()
    {
        return new TermCollection($this->getEntity()->getTerms(), $this->getSharedTaxonomyManager());
    }

    public function setTerms($terms)
    {
        return $this->getEntity()->setTerms($terms);
    }

    /**
     *
     * @return SharedTaxonomyManager $manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     *
     * @param SharedTaxonomyManager $manager            
     * @return $this
     */
    public function setManager(SharedTaxonomyManager $manager)
    {
        $this->manager = $manager;
        return $this;
    }

    protected function getRootTermEntities()
    {
        $collection = new ArrayCollection();
        $terms = $this->getEntity()->getTerms();
        foreach ($terms as $entity) {
            if (! $entity->hasParent() || ($entity->hasParent() && $entity->getParent()->getTaxonomy() !== $this->getEntity())) {
                $collection->add($entity);
            }
        }
        return $collection;
    }

    private function hydrateTerm(array $data, TermTaxonomyEntityInterface $term)
    {
        $columns = array(
            'parent' => 'setParent',
            'description' => 'setDescription',
            'weight' => 'setWeight'
        );
        
        foreach ($columns as $key => $method) {
            if (array_key_exists($key, $data)) {
                $term->$method($data[$key]);
            }
        }
        
        return $this;
    }
}