<?php

namespace Wurstpress\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wurstpress\CoreBundle\Common\CreatedUpdatedTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Wurstpress\CoreBundle\Entity\DocumentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Document
{
    use CreatedUpdatedTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type", type="string", length=255)
     */
    private $mimeType;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="documents")
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="id", nullable=true)
     */
    protected $collection;

    /**
     * @Assert\File(maxSize="6000000")
     * @Assert\NotBlank()
     */
    private $file;

    /**
     * non database properties
     */
    private $web_dir;
    private $upload_dir = 'uploads/documents';


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
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return Document
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Document
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get file
     *
     * @return $file
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file
     *
     * @param $file
     * @return Document
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param null $web_dir
     * @param null $upload_dir
     */
    public function __construct($web_dir = null, $upload_dir = null)
    {
        $this->web_dir = $web_dir ?: sprintf('%s/../../../../web', __DIR__);
        $this->upload_dir = $upload_dir ?: 'uploads/documents';
    }

    /**
     * @param string $dir
     * @return Document
     */
    public function setWebDir($dir)
    {
        $this->web_dir = $dir;

        return $this;
    }

    /**
     * @param $dir
     * @return Document
     */
    public function setUploadDir($dir)
    {
        $this->upload_dir = $dir;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : sprintf('%s/%s', $this->getUploadRootDir(), $this->path);
    }

    /**
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : sprintf('%s/%s', $this->upload_dir, $this->path);
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return sprintf('%s/%s', $this->web_dir, $this->upload_dir);
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile())
        {
            $this->path = sprintf('%s.%s', sha1(uniqid(mt_rand(), true)), $this->getFile()->guessExtension());
            $this->setName($this->getFile()->getClientOriginalName());
            $this->setSize($this->getFile()->getClientSize());
            $this->setMimeType($this->getFile()->getClientMimeType());
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) unlink($file);
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) return;

        $this->file->move($this->getUploadRootDir(), $this->path);

        unset($this->file);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getWebPath() ?: '';
    }

    public function isImage()
    {
        return in_array($this->getMimeType(), [ 'image/jpeg', 'image/png', 'image/gif' ]);
    }

    /**
     * Set collection
     *
     * @param \Wurstpress\CoreBundle\Entity\Collection $collection
     * @return Document
     */
    public function setCollection(\Wurstpress\CoreBundle\Entity\Collection $collection = null)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return \Wurstpress\CoreBundle\Entity\Collection 
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
