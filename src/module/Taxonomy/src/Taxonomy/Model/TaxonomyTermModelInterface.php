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
namespace Taxonomy\Model;

use Taxonomy\Entity\TaxonomyInterface;
use Taxonomy\Model\TaxonomyTermModelInterface;
use Doctrine\Common\Collections\Collection;
use Language\Entity\LanguageEntityInterface;
use Term\Model\TermModelAwareInterface;
use Taxonomy\Entity\TaxonomyTypeInterface;


interface TaxonomyTermModelInterface extends TermModelAwareInterface
{

    /**
     * 
     * @return int
     */
    public function getId();

    /**
     * 
     * @return string
     */
    public function getDescription();

    /**
     *
     * @return bool
     */
    public function hasParent();
    
    /**
     * 
     * @return TaxonomyTermModelInterface
     */
    public function getEntity();

    /**
     *
     * @return bool
     */
    public function hasChildren();
    
    /**
     * 
     * @return TaxonomyInterface
     */
    public function getTaxonomy();
    
    /**
     * 
     * @return TaxonomyTypeInterface
     */
    public function getType();
    
    /**
     * 
     * @return Collection
     */
    public function getChildren();
    
    /**
     * 
     * @return self
     */
    public function getParent();

    /**
     *
     * @return string
     */
    public function getName();

    /**
     *
     * @return string
     */
    public function getSlug();

    /**
     *
     * @return int
     */
    public function getPosition();
    
    /**
     * 
     * @return LanguageEntityInterface
     */
    public function getLanguage();
    
    /**
     * 
     * @param string $association
     * @return TaxonomyTermModelAwareInterface[]
     */
    public function getAssociated($association);
    
    /**
     * 
     * @param string $association
     * @return int
     */
    public function countAssociations($association);
    
    /**
     * 
     * @param string $association
     * @param TaxonomyTermModelAwareInterface $object
     * @return bool
     */
    public function isAssociated($association, TaxonomyTermModelAwareInterface $object);
    
    /**
     * 
     * @param string $association
     * @param TaxonomyTermModelAwareInterface $object
     * @return $this
     */
    public function associateObject($association, TaxonomyTermModelAwareInterface $object);
    
    /**
     * 
     * @param string $association
     * @param int $objectId
     * @param int $position
     * @return $this
     */
    public function positionAssociatedObject($association, $objectId, $position);
    
    /**
     * 
     * @param string $field
     * @param TaxonomyTermModelAwareInterface $object
     * @return $this
     */
    public function removeAssociation($field, TaxonomyTermModelAwareInterface $object);
    
    /**
     * 
     * @param TaxonomyInterface $taxonomy
     * @return $this
     */
    public function setTaxonomy(TaxonomyInterface $taxonomy);
    
    /**
     * 
     * @param string $description
     * @return $this
     */
    public function setDescription($description);
    
    /**
     * 
     * @param self $parent
     * @return $this
     */
    public function setParent(self $parent);
    
    /**
     * 
     * @param int $position
     * @return $this
     */
    public function setPosition($position);
    
    /**
     * 
     * @param string $name
     * @return TaxonomyTermModelInterface
     */
    public function findAncestorByTypeName($name);
    
    /**
     * 
     * @param TaxonomyTermModelInterface $ancestor
     * @return bool
     */
    public function knowsAncestor(TaxonomyTermModelInterface $ancestor);
}