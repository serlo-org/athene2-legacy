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
namespace Blog\Entity;

use Blog\Exception;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Instance\Entity\InstanceAwareTrait;
use Taxonomy\Entity\TaxonomyTermInterface;
use Taxonomy\Entity\TaxonomyTermNodeInterface;
use User\Entity\UserInterface;
use Uuid\Entity\Uuid;

/**
 * A blog post.
 *
 * @ORM\Entity
 * @ORM\Table(name="blog_post")
 */
class Post extends Uuid implements PostInterface
{
    use InstanceAwareTrait;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    /**
     * @ORM\ManyToOne(targetEntity="Taxonomy\Entity\TaxonomyTerm")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $blog;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="string")
     */
    protected $content;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $publish;

    public function __construct()
    {
        $this->publish = new DateTime();
        $this->date    = new DateTime();
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getBlog()
    {
        return $this->blog;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getTimestamp()
    {
        return $this->date;
    }

    public function getPublish()
    {
        return $this->publish;
    }

    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;

        return $this;
    }

    public function setBlog(TaxonomyTermInterface $category)
    {
        $this->blog = $category;

        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function setTimestamp(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    public function setPublish(DateTime $publish = null)
    {
        $this->publish = $publish;

        return $this;
    }

    public function isPublished()
    {
        return $this->getPublish() < new DateTime();
    }

    public function addTaxonomyTerm(TaxonomyTermInterface $taxonomyTerm, TaxonomyTermNodeInterface $node = null)
    {
        $this->setBlog($taxonomyTerm);
    }

    public function removeTaxonomyTerm(TaxonomyTermInterface $taxonomyTerm, TaxonomyTermNodeInterface $node = null)
    {
        throw new Exception\RuntimeException('You can\'t unset the category - it is required!');
    }

    public function getTaxonomyTerms()
    {
        return new ArrayCollection((array)$this->getBlog());
    }
}