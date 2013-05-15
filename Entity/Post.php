<?php

namespace Wurstpress\CoreBundle\Entity;

use DoctrineExtensions\Taggable\Doctrine;
use DoctrineExtensions\Taggable\Taggable;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Wurstpress\CoreBundle\Common\CreatedUpdatedTrait;


/**
 * Wurstpress\CoreBundle\Entity\Post
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Wurstpress\CoreBundle\Entity\PostRepository")
 */
class Post implements Taggable
{
    use CreatedUpdatedTrait;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string $content
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string $slug
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var ArrayCollection $comments
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
     */
    private $comments;

    /**
     * @var $tags
     */
    private $tags;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add comments
     *
     * @param Comment $comments
     * @return Post
     */
    public function addComment(Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param Comment $comments
     */
    public function removeComment(Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return DoctrineCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Overrides current comments
     *
     * @param DoctrineCollection $comments
     * @return Post
     */
    public function setComments(DoctrineCollection $comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * toString implementation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * Returns the unique taggable resource type
     *
     * @return string
     */
    public function getTaggableType()
    {
        return 'wurstpress_post';
    }

    /**
     * Returns the unique taggable resource identifier
     *
     * @return string
     */
    public function getTaggableId()
    {
        return $this->getId();
    }

    /**
     * Returns the collection of tags for this Taggable entity
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        $this->tags = $this->tags ?: new ArrayCollection();

        return $this->tags;
    }
}
