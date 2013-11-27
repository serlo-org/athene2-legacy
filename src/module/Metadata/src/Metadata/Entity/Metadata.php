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
namespace Metadata\Entity;

use Doctrine\ORM\Mapping as ORM;
use Uuid\Entity\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata")
 */
class Metadata implements MetadataInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $value;

    /**
     * @ORM\ManyToOne(targetEntity="Uuid\Entity\Uuid")
     * @ORM\JoinColumn(name="uuid_id", referencedColumnName="id")
     */
    protected $object;

    /**
     * @ORM\ManyToOne(targetEntity="MetadataKey")
     */
    protected $key;

    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function setObject(UuidInterface $object)
    {
        $this->object = $object;
        return $this;
    }

    public function setKey(MetadataKeyInterface $key)
    {
        $this->key = $key;
        return $this;
    }
}