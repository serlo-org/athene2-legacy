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
namespace Entity\Entity;

use Doctrine\ORM\Mapping as ORM;
use Versioning\Entity\RepositoryInterface;
use Link\Entity\LinkEntityInterface;
use Uuid\Entity\UuidEntity;

/**
 * An entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="entity")
 */
class Entity extends UuidEntity implements RepositoryInterface, LinkEntityInterface, EntityInterface
{

    /**
     * @ORM\ManyToMany(targetEntity="Entity", inversedBy="children", cascade={"persist"})
     * @ORM\JoinTable(name="entity_link",
     * joinColumns={
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id")
     * },
     * inverseJoinColumns={
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * }
     * )
     */
    protected $parents;

    /**
     * @ORM\ManyToMany(targetEntity="Entity", mappedBy="parents", cascade={"persist"})
     * @ORM\JoinTable(
     * name="entity_link",
     * joinColumns={
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id")
     * },
     * inverseJoinColumns={
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * }
     * )
     */
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="Revision", mappedBy="repository")
     * @ORM\OrderBy({"date" = "DESC"})
     */
    protected $revisions;

    /**
     * @ORM\OneToOne(targetEntity="Revision")
     * @ORM\JoinColumn(name="current_revision_id", referencedColumnName="id")
     */
    protected $currentRevision;

    /**
     * @ORM\ManyToMany(targetEntity="\Taxonomy\Entity\TermTaxonomy", cascade={"persist"})
     * @ORM\JoinTable(name="term_taxonomy_entity",
     * inverseJoinColumns={@ORM\JoinColumn(name="term_taxonomy_id", referencedColumnName="id")},
     * joinColumns={@ORM\JoinColumn(name="entity_id", referencedColumnName="id")}
     * )
     */
    protected $terms;

    /**
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="entities")
     * @ORM\JoinColumn(name="entity_type_id", referencedColumnName="id")
     */
    protected $type;

    /**
     * @ORM\Column(type="datetime", options={"default"="CURRENT_TIMESTAMP"})
     */
    protected $date;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $trashed;

    /**
     * @ORM\ManyToOne(targetEntity="Language\Entity\Language", inversedBy="entities")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    protected $language;
    
    protected $fieldOrder = array();
    
    /**
     * @return field_type $terms
     */
    public function getTerms ()
    {
        return $this->terms;
    }

	/**
     * @param field_type $terms
     * @return $this
     */
    public function setTerms ($terms)
    {
        $this->terms = $terms;
        return $this;
    }

	public function setFieldOrder(array $fieldOrder){
        $this->fieldOrder = $fieldOrder;
        return $this;
    }
    
    public function getFieldOrder($field){
        return array_search($field, $this->fieldOrder);
    }
    
    /**
     * @param field_type $type
     * @return $this
     */
    public function setType ($type)
    {
        $this->type = $type;
        return $this;
    }

	/**
     * @return field_type $issues
     */
    public function getIssues ()
    {
        return $this->issues;
    }

	/**
     * @param field_type $issues
     * @return $this
     */
    public function setIssues ($issues)
    {
        $this->issues = $issues;
        return $this;
    }

	/**
     * @return field_type $currentRevision
     */
    public function getCurrentRevision ()
    {
        return $this->currentRevision;
    }

	/**
     * @return field_type $type
     */
    public function getType ()
    {
        return $this->type;
    }

	/**
     * @return field_type $language
     */
    public function getLanguage ()
    {
        return $this->language;
    }

	/**
     * @return field_type $date
     */
    public function getDate ()
    {
        return $this->date;
    }

	/**
     * @param \Doctrine\Common\Collections\ArrayCollection $parents
     * @return $this
     */
    public function setParents ($parents)
    {
        $this->parents = $parents;
        return $this;
    }

	/**
     * @param \Doctrine\Common\Collections\ArrayCollection $children
     * @return $this
     */
    public function setChildren ($children)
    {
        $this->children = $children;
        return $this;
    }

	/**
     * @param \Doctrine\Common\Collections\ArrayCollection $revisions
     * @return $this
     */
    public function setRevisions ($revisions)
    {
        $this->revisions = $revisions;
        return $this;
    }

	/**
     * @param field_type $currentRevision
     * @return $this
     */
    public function setCurrentRevision ($currentRevision)
    {
        $this->currentRevision = $currentRevision;
        return $this;
    }

	/**
     * @param field_type $factory
     * @return $this
     */
    public function setFactory ($factory)
    {
        $this->type = $factory;
        return $this;
    }

	/**
     * @param field_type $language
     * @return $this
     */
    public function setLanguage ($language)
    {
        $this->language = $language;
        return $this;
    }

	/**
     * @param field_type $date
     * @return $this
     */
    public function setDate ($date)
    {
        $this->date = $date;
        return $this;
    }

	public function __construct($uuid)
    {
        $this->revisions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->parents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->issues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->trashed = false;
        return parent::__construct($uuid);
    }

    /**
     * (non-PHPdoc)
     * @see \Versioning\Entity\RepositoryInterface::addRevision()
     */
    public function newRevision()
    {
        $revision = new Revision();
        $revision->setRepository($this);
        return $revision;
    }

    /**
     * (non-PHPdoc)
     * @see \Versioning\Entity\RepositoryInterface::getRevisions()
     */
    public function getRevisions()
    {
        return $this->revisions;
    }
    
    /*
     * (non-PHPdoc) @see \Link\Entity\LinkEntityInterface::getChildren()
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /*
     * (non-PHPdoc) @see \Link\Entity\LinkEntityInterface::getParents()
     */
    public function getParents()
    {
        return $this->parents;
    }
    
    public function trash()
    {
        $this->trashed = true;
        return $this;
    }
    
    public function unTrash(){
        $this->trashed = false;
        returN $this;
    }
    
    public function isTrashed(){
        return $this->trashed;
    }
}