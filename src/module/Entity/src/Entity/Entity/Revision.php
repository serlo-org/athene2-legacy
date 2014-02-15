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
namespace Entity\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use User\Entity\UserInterface;
use Uuid\Entity\Uuid;
use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;

/**
 * An entity link.
 *
 * @ORM\Entity
 * @ORM\Table(name="entity_revision")
 */
class Revision extends Uuid implements RevisionInterface
{

    /**
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="revisions")
     */
    protected $repository;

    /**
     * @ORM\OneToMany(targetEntity="RevisionField", mappedBy="revision", cascade={"persist"})
     */
    protected $fields;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User")
     */
    protected $author;

    /**
     * @ORM\Column(type="datetime", options={"default"="CURRENT_TIMESTAMP"})
     */
    protected $date;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    public function delete()
    {
        return $this;
    }

    public function get($field)
    {
        $field = $this->getField($field);

        if (!is_object($field)) {
            return $field;
        }

        return $field->getValue();
    }

    protected function getField($field)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("field", $field))->setFirstResult(0)->setMaxResults(
            1
        );

        $data = $this->fields->matching($criteria);

        if (empty($data)) {
            return null;
        }

        return $data[0];
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getTimestamp()
    {
        return $this->date;
    }

    public function set($name, $value)
    {
        $entity = $this->getField($name);

        if (!is_object($entity)) {
            $entity = new RevisionField($name, $this->getId());
            $this->fields->add($entity);
        }

        $entity->set('field', $name);
        $entity->set('revision', $this);
        $entity->set('value', $value);

        return $entity;
    }

    public function setTimestamp(\DateTime $date)
    {
        $this->date = $date;
    }

    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }
}
