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
use Instance\Entity\InstanceInterface;
use Instance\Entity\InstanceProviderInterface;
use Uuid\Entity\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="event_parameter_uuid")
 */
class EventParameterUuid implements InstanceProviderInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="EventParameter", inversedBy="object")
     * @ORM\JoinColumn(name="event_parameter_id", referencedColumnName="id")
     */
    protected $eventParameter;

    /**
     * @ORM\ManyToOne(targetEntity="Uuid\Entity\Uuid")
     */
    protected $uuid;

    /**
     * @param EventParameterInterface $eventParameter
     * @param UuidInterface           $uuid
     */
    public function __construct(EventParameterInterface $eventParameter, UuidInterface $uuid)
    {
        $this->eventParameter = $eventParameter;
        $this->uuid           = $uuid;
    }

    /**
     * @return EventParameterInterface
     */
    public function getEventParameter()
    {
        return $this->eventParameter;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return InstanceInterface
     */
    public function getInstance()
    {
        return $this->eventParameter->getInstance();
    }

    /**
     * @return UuidInterface
     */
    public function getValue()
    {
        return $this->uuid;
    }
}
