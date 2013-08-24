<?php
/**
 *
 * @author Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @copyright 2013 by www.serlo.org
 * @license LGPL
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
namespace Taxonomy\Service;

use Core\Entity\EntityInterface;
use Taxonomy\Factory\FactoryInterface;
use Taxonomy\Exception\LinkNotAllowedException;
use Taxonomy\Exception\InvalidArgumentException;
use Taxonomy\Entity\TermTaxonomyEntityInterface;
use Taxonomy\Collection\TermCollection;

class TermService implements TermServiceInterface
{
    
    use\ Zend\ServiceManager\ServiceLocatorAwareTrait,\Term\Manager\TermManagerAwareTrait, \Common\Traits\EntityDelegatorTrait;

    protected $options = array(
        'options' => array(
            'allowed_parents' => array(),
            'allowed_links' => array(),
            'radix_enabled' => true
        )
    );
    
    /**
     *
     * @var \Taxonomy\Manager\TermManagerInterface
     */
    protected $manager;
    
    /*
     * (non-PHPdoc) @see \Taxonomy\TermManagerAwareInterface::getTermManager()
     */
    public function getManager()
    {
        return $this->manager;
    }
    
    /*
     * (non-PHPdoc) @see \Taxonomy\TermManagerAwareInterface::setTermManager()
     */
    public function setManager(\Taxonomy\Manager\TermManagerInterface $termManager)
    {
        $this->manager = $termManager;
        return $this;
    }
    
    /*
     * (non-PHPdoc) @see \Taxonomy\Service\TermServiceInterface::getParent()
     */
    public function getParent()
    {
        return $this->getManager()->get($this->getEntity()
            ->getParent());
    }
    
    /*
     * (non-PHPdoc) @see \Taxonomy\Service\TermServiceInterface::getChildren()
     */
    public function getChildren()
    {
        /*
         * return $this->getManager()->get($this->getEntity() ->get('children'));
         */
        return new TermCollection($this->getEntity()->get('children'), $this->getManager()->getManager());
    }
    
    /*
     * (non-PHPdoc) @see \Taxonomy\Service\TermServiceInterface::getAllLinks()
     */
    public function getAllLinks()
    {
        $return = array();
        foreach ($this->getAllowedLinks() as $targetField => $callback) {
            $return[$targetField] = $this->getLinks($targetField);
        }
        return $return;
    }
    
    /*
     * (non-PHPdoc) @see \Taxonomy\Service\TermServiceInterface::getLinks()
     */
    public function getLinks($targetField)
    {
        $this->linkAllowedWithException($targetField);
        $links = $this->getAllowedLinks();
        $closure = $links[$targetField];
        return $closure($this->get($targetField));
    }
    
    /*
     * (non-PHPdoc) @see \Taxonomy\Service\TermServiceInterface::addLink()
     */
    public function addLink($targetField, $target)
    {
        $target = $this->findEntity($target);
        $this->linkAllowedWithException($targetField);
        $entity = $this->getEntity();
        $entity->get($targetField)->add($target);
        $this->persist();
        return $this;
    }
    
    /*
     * (non-PHPdoc) @see \Taxonomy\Service\TermServiceInterface::removeLink()
     */
    public function removeLink($targetField, $target)
    {
        $target = $this->findEntity($target);
        $this->linkAllowedWithException($targetField);
        $entity = $this->getEntity();
        $entity->get($targetField)->remove($target);
        $this->persist();
        return $this;
    }

    private function findEntity($target)
    {
        return $target;
        //throw new InvalidArgumentException();
    }

    public function hasLink($targetField, $target)
    {
        $target = $this->findEntity($target);
        $this->linkAllowedWithException($targetField);
        $entity = $this->getEntity();
        return $entity->get($targetField)->containsKey($target->getId());
    }

    protected function linkAllowedWithException($targetField)
    {
        if (! $this->linkAllowed($targetField))
            throw new LinkNotAllowedException();
    }

    public function getAllowedLinks()
    {
        return $this->allowedLinks;
    }
    
    /*
     * (non-PHPdoc) @see \Taxonomy\TaxonomyManagerInterface::enableLink()
     */
    public function enableLink($targetField, \Closure $callback)
    {
        $this->allowedLinks[$targetField] = $callback;
        return $this;
    }
    
    /*
     * (non-PHPdoc) @see \Taxonomy\TaxonomyManagerInterface::linkingAllowed()
     */
    public function linkAllowed($targetField)
    {
        return isset($this->allowedLinks[$targetField]);
    }

    public function build()
    {
        // read factory class from db
        $factoryClassName = $this->getEntity()->getFactory();
        
        if (! $factoryClassName)
            throw new \Exception('Factory not set');
        
        $factoryClassName = $factoryClassName->getName();
        
        if (! class_exists($factoryClassName))
            throw new \Exception("Clas `{$factoryClassName}` not found");
        
        $factory = new $factoryClassName();
        if (! $factory instanceof FactoryInterface)
            throw new \Exception('Something somewhere went terribly wrong.');
        
        return $factory->build($this);
    }

    public function update(array $data)
    {
        $merged = array_merge(array(
            'term' => array(
                'name' => $this->getName()
            ),
            'parent' => $this->getParent(),
            'taxonomy' => $this->getTaxonomy()
        ), $data);
        
        $this->setName($data['term']['name']);
        unset($data['term']);
        try {
            $this->populate($data);
        } catch (\Core\Exception\UnknownPropertyException $e) {}
        $this->persistAndFlush();
        return $this;
    }

    public function setName($name)
    {
        $term = $this->getTermManager()->get($name);
        $this->getEntity()->set('term', $term->getEntity());
        return $this;
    }

    protected $allowedParentFactories = array();

    protected $allowedParentTaxonomy = array();

    protected $radix = false;

    public function parentNodeAllowed(TermTaxonomyEntityInterface $term)
    {
        return array_key_exists($term->getTaxonomy()->getId(), $this->allowedParentTaxonomy) || array_key_exists($term->getTaxonomy()
            ->getFactory()
            ->getId(), $this->allowedParentFactories);
    }

    public function allowParentNodesByFactory($id)
    {
        $this->allowedParentFactories[$id] = true;
        return $this;
    }

    public function allowParentNodesByTaxonomy($id)
    {
        $this->allowedParentTaxonomy[$id] = true;
        return $this;
    }

    public function radixEnabled()
    {
        return $this->options['radix_enabled'];
    }

    public function setParent($parent)
    {
        $entity = $this->getEntity();
        if ($parent == NULL) {
            if ($this->radixEnabled()) {
                $entity->setParent($parent);
            } else {
                throw new InvalidArgumentException('Radix not allowed.');
            }
        } else {
            if ($this->parentNodeAllowed($parent)) {
                $entity->setParent($parent);
            } else {
                throw new InvalidArgumentException('Parent `' . $parent->getId() . '` not allowed for `' . $entity->getId() . '`.');
            }
        }
    }
	/**
     * @return multitype:multitype:multitype: string   $options
     */
    public function getOptions()
    {
        return $this->options;
    }

	/**
     * @param multitype:multitype:multitype: string   $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = array_merge_recursive($this->options, $options);
        return $this;
    }

 
}