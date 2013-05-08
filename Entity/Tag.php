<?php

namespace Wurstpress\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FPN\TagBundle\Entity\Tag as BaseTag;

/**
 * Tag
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Tag extends BaseTag
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
     * @ORM\OneToMany(targetEntity="Tagging", mappedBy="tag", fetch="EAGER")
     */
    protected $tagging;


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
     * Set tagging
     *
     * @param integer $tagging
     * @return Tag
     */
    public function setTagging($tagging)
    {
        $this->tagging = $tagging;
    
        return $this;
    }

    /**
     * Get tagging
     *
     * @return integer 
     */
    public function getTagging()
    {
        return $this->tagging;
    }
}
