<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Event\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 */
class Event implements EventInterface
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
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    public function __toString()
    {
        return $this->getName();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setName($uri)
    {
        $this->name = $uri;

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}