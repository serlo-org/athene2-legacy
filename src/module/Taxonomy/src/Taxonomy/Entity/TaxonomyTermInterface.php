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
namespace Taxonomy\Entity;

use Instance\Entity\InstanceProviderInterface;

interface TaxonomyTermInterface extends InstanceProviderInterface
{
    /**
     * @param string                     $association
     * @param TaxonomyTermAwareInterface $object
     * @return self
     */
    public function associateObject($association, TaxonomyTermAwareInterface $object);

    /**
     * @param string $association
     * @return int
     */
    public function countAssociations($association);

    /**
     * @param string $name
     * @return TaxonomyTermModelInterface
     */
    public function findAncestorByTypeName($name);

    /**
     * @param array $slugs
     * @return self
     */
    public function findChildBySlugs(array $slugs);

    /**
     * @param string $association
     * @return TaxonomyTermAwareInterface[]
     */
    public function getAssociated($association);

    /**
     * @return Collection
     */
    public function getChildren();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return self
     */
    public function getParent();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @return TaxonomyInterface
     */
    public function getTaxonomy();

    /**
     * @return TaxonomyTypeInterface
     */
    public function getType();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @return bool
     */
    public function hasParent();

    /**
     * @param string                     $association
     * @param TaxonomyTermAwareInterface $object
     * @return bool
     */
    public function isAssociated($association, TaxonomyTermAwareInterface $object);

    /**
     * @param self $ancestor
     * @return bool
     */
    public function knowsAncestor(self $ancestor);

    /**
     * @param string $association
     * @param int    $objectId
     * @param int    $position
     * @return self
     */
    public function positionAssociatedObject($association, $objectId, $position);

    /**
     * @param string                     $field
     * @param TaxonomyTermAwareInterface $object
     * @return self
     */
    public function removeAssociation($field, TaxonomyTermAwareInterface $object);

    /**
     * @param string $description
     * @return self
     */
    public function setDescription($description);

    /**
     * @param self $parent
     * @return self
     */
    public function setParent(self $parent);

    /**
     * @param int $position
     * @return self
     */
    public function setPosition($position);

    /**
     * @param TaxonomyInterface $taxonomy
     * @return self
     */
    public function setTaxonomy(TaxonomyInterface $taxonomy);

    /**
     * @param string $stopAtType
     * @param string $delimiter
     * @return string
     */
    public function slugify($stopAtType = null, $delimiter = '/');
}