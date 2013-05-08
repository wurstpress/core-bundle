<?php

namespace Wurstpress\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FPN\TagBundle\Entity\Tagging as BaseTagging;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Tagging
 *
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="tagging_idx", columns={"tag_id", "resource_type", "resource_id"})})
 * @ORM\Entity
 */
class Tagging extends BaseTagging
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Tag",cascade={"persist"},inversedBy="tagging")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     */
    protected $tag;


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
     * Set tag
     *
     * @param integer $tag
     * @return Tagging
     */
    public function setTag(\DoctrineExtensions\Taggable\Entity\Tag $tag)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return integer 
     */
    public function getTag()
    {
        return $this->tag;
    }
}
