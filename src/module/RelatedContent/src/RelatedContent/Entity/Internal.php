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
namespace RelatedContent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Uuid\Entity\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="related_content_internal")
 */
class Internal implements InternalInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Container", inversedBy="externalRelations")
     */
    protected $container;

    /**
     * @ORM\ManyToOne(targetEntity="Uuid\Entity\Uuid")
     */
    protected $reference;

    /**
     * @ORM\Column(type="string") *
     */
    protected $title;
    
    public function getReference ()
    {
        return $this->reference;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getContainer()
    {
        return $this->container;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    public function setReference (UuidInterface $object)
    {
        $this->reference = $object;
        return $this;
    }
}