<?php

namespace AppBundle\Entity;

use AppBundle\Repository\ArticleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Stringy\StaticStringy;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Article
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 * @ORM\Table(name="articles")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    /**
     * @var integer;
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var string
     * @Assert\NotBlank(message="Please fill in the name of the article")
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $perex;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="Please fill in the text of the article")
     */
    protected $text;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $display;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $deleted;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    protected $main_img;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $images = [];

    /**
     * @var mixed
     * @Assert\NotNull(message="Please select a rubric this article belong to.")
     * 
     * @ORM\ManyToOne(inversedBy="articles", targetEntity="AppBundle\Entity\Rubric")
     */
    protected $rubric;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="article")
     */
    protected $comments;

    /**
     * @var UploadedFile
     * @Assert\Image()
     */
    protected $tmpMainImgFile;

    public function __construct()
    {
        $this->setDeleted(false);
        $this->setDisplay(true);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt) 
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() : ?\DateTime
    {
        return $this->created_at;
    }

    /**
     * Set perex
     *
     * @param string $perex
     */
    public function setPerex($perex) 
    {
        $this->perex = $perex;
    }

    /**
     * Get perex
     *
     * @return string
     */
    public function getPerex() : ?string
    {
        return $this->perex;
    }

    /**
     * Set text
     *
     * @param string $text
     */
    public function setText($text) 
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText() : ?string
    {
        return $this->text;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name) 
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url) 
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl() : ?string
    {
        return $this->url;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     */
    public function setDeleted($deleted) 
    {
        $this->deleted = $deleted;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted() : bool
    {
        return $this->deleted;
    }

    /**
     * Set mainImg
     *
     * @param string $mainImg
     */
    public function setMainImg($mainImg) 
    {
        $this->main_img = $mainImg;
    }

    /**
     * Get mainImg
     *
     * @return string
     */
    public function getMainImg() : ?string
    {
        return $this->main_img;
    }

    /**
     * Set images
     *
     * @param array $images
     */
    public function setImages($images) 
    {
        $this->images = $images;
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages() : array
    {
        return $this->images;
    }

    /**
     * Set display
     *
     * @param boolean $display
     */
    public function setDisplay($display) 
    {
        $this->display = $display;
    }

    /**
     * Get display
     *
     * @return boolean
     */
    public function getDisplay() : bool
    {
        return $this->display;
    }

    /**
     * @return null|string
     */
    public function getImageDir() : ?string
    {
        return $this->getId() ? '/assets/images/articles/' . $this->getId() . '/' : null;
    }

    /**
     * @return bool
     */
    public function isMainImg() : bool
    {
        return $this->getMainImg() && file_exists(WEB_DIR . $this->getImageDir() . $this->getMainImg());
    }

    /**
     * @return null|string
     */
    public function getMainImagePath() : ?string
    {
        return !empty($this->getMainImg()) && !($this->getMainImg() instanceof UploadedFile && $this->isMainImg()) ? $this->getImageDir() . $this->getMainImg() : null;
    }

    /**
     * Set rubric
     *
     * @param \AppBundle\Entity\Rubric|int $rubric
     */
    public function setRubric($rubric = null) 
    {
        $this->rubric = $rubric;
    }

    /**
     * Get rubric
     *
     * @return Rubric
     */
    public function getRubric() : ?Rubric
    {
        return $this->rubric;
    }

    /**
     * @return Comment[]|Collection
     */
    public function getComments() : Collection
    {
        return $this->comments;
    }

    /**
     * @ORM\PreUpdate
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event) 
    {
        if($event->hasChangedField('perex') && strlen($this->getPerex()) > 200){
            $this->setPerex(substr($this->getPerex(), 0, 200) . '...');
        }
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() 
    {
        $this->setCreatedAt(new \DateTime());

        if(empty($this->getPerex())){
            if(strlen($this->getText()) > 200){
                $this->setPerex(substr(StaticStringy::htmlDecode(strip_tags($this->getText())), 0, 200) . '...');
            }
            else{
                $this->setPerex($this->getText());
            }
        }
    }

    /**
     * @return UploadedFile
     */
    public function getTmpMainImgFile() : ?UploadedFile
    {
        return $this->tmpMainImgFile;
    }

    /**
     * @param UploadedFile $tmpMainImgFile
     */
    public function setTmpMainImgFile($tmpMainImgFile) 
    {
        $this->tmpMainImgFile = $tmpMainImgFile;
    }
}
