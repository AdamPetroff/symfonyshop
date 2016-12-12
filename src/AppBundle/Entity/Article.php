<?php

namespace AppBundle\Entity;

use AppBundle\Repository\ArticleRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Stringy\StaticStringy;
use Stringy\Stringy;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGenerator;
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
     *
     * @Assert\NotBlank()
     */
    protected $text;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $display;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $news;

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
     * @Assert\Image()
     */
    protected $main_img;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $images;

    /**
     * @var mixed
     * 
     * @ORM\ManyToOne(inversedBy="articles", targetEntity="AppBundle\Entity\Rubric")
     */
    protected $rubric;

    public function __construct()
    {
        $this->setDeleted(false);
        $this->setNews(true);
        $this->setDisplay(true);
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Article
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
        
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set perex
     *
     * @param string $perex
     *
     * @return Article
     */
    public function setPerex($perex)
    {
        $this->perex = $perex;

        return $this;
    }

    /**
     * Get perex
     *
     * @return string
     */
    public function getPerex()
    {
        return $this->perex;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Article
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Article
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
     * Set url
     *
     * @param string $url
     *
     * @return Article
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set news
     *
     * @param boolean $news
     *
     * @return Article
     */
    public function setNews($news)
    {
        $this->news = $news;

        return $this;
    }

    /**
     * Get news
     *
     * @return boolean
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Article
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set mainImg
     *
     * @param string $mainImg
     *
     * @return Article
     */
    public function setMainImg($mainImg)
    {
        $this->main_img = $mainImg;

        return $this;
    }

    /**
     * Get mainImg
     *
     * @return string
     */
    public function getMainImg()
    {
        return $this->main_img;
    }

    /**
     * Set images
     *
     * @param array $images
     *
     * @return Article
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set display
     *
     * @param boolean $display
     *
     * @return Article
     */
    public function setDisplay($display)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * Get display
     *
     * @return boolean
     */
    public function getDisplay()
    {
        return $this->display;
    }

    public function getImageDir()
    {
        return $this->getId() ? '/assets/images/articles/' . $this->getId() . '/' : null;
    }

    public function isMainImg()
    {
        return $this->getMainImg() && file_exists(WEB_DIR . $this->getImageDir() . $this->getMainImg());
    }

    public function getMainImagePath()
    {
        return !empty($this->getMainImg()) && !($this->getMainImg() instanceof UploadedFile && $this->isMainImg()) ? $this->getImageDir() . $this->getMainImg() : null;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function beforeSaving()
    {
        if(empty($this->getCreatedAt())){
            $this->setCreatedAt(new \DateTime());
        }
        if(empty($this->getPerex())){
            if(strlen($this->getText()) > 200){
                $this->setPerex(substr($this->getText(), 0, 200) . '...');
            }
            else{
                $this->setPerex($this->getText());
            }
        }
    }


    /**
     * Set rubric
     *
     * @param \AppBundle\Entity\Rubric|int $rubric
     *
     * @return Article
     */
    public function setRubric($rubric = null)
    {
        $this->rubric = $rubric;

        return $this;
    }

    /**
     * Get rubric
     *
     * @return \AppBundle\Entity\Rubric
     */
    public function getRubric()
    {
        return $this->rubric;
    }
}
