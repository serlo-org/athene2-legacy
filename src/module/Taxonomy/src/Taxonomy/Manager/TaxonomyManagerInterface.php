<?php
/**
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

use Language\Service\LanguageServiceInterface;
use Taxonomy\Service\TermServiceInterface;
use Taxonomy\Collection\TermCollection;
use Taxonomy\Entity\TaxonomyTypeInterface;

interface TaxonomyManagerInterface
{

    /**
     *
     * @param numeric $id            
     * @return TermServiceInterface
     */
    public function getTerm($id);

    /**
     * Finds a Term by its ancestors.
     *
     * <code>
     * $param = array(0 => 'path', 1 => 'to', 2 => 'something');
     * $this->findTermByAncestors(); // returns "something"
     * </code>
     *
     * Note that the first array element needs to be a sapling node
     *
     * @param array $ancestors            
     * @return TermServiceInterface
     */
    public function findTermByAncestors(array $ancestors);

    /**
     * Returns the nodes on the highest level.
     * Nodes on the highest level do either not have a parent node or do have a different taxonomy type than their parents.
     *
     * @return \Taxonomy\Collection\TermCollection
     */
    public function getSaplings();

    /**
     *
     * @return array
     */
    public function getAllowedChildrenTypeNames();

    /**
     *
     * @param string $type            
     * @return bool
     */
    public function allowsParentType($type);

    /**
     *
     * @return array
     */
    public function getAllowedParentTypeNames();

    /**
     *
     * @return int $id
     */
    public function getId();

    /**
     *
     * @return TaxonomyTypeInterface
     */
    public function getType();

    /**
     *
     * @param TaxonomyTypeInterface $type            
     */
    public function setType(TaxonomyTypeInterface $type);

    /**
     *
     * @return TermCollection
     */
    public function getTerms();
}