<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Rubric
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="APpBundle\Repository\RubricRepository") @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="rubrics")
 */
class Rubric
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue;
     * @ORM\Column(type="integer")
     */
    protected $id;

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
    protected $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

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
     * @var mixed
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rubric", inversedBy="children")
     */
    protected $parent;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rubric", mappedBy="parent")
     */
    protected $children;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $special_access;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @var mixed
     *
     * @ORM\OneToMany(mappedBy="rubric", targetEntity="AppBundle\Entity\Article")
     */
    protected $articles;


    public function __construct()
    {
        $this->special_access = 0;
        $this->active = false;
        $this->deleted = false;
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
     * Set name
     *
     * @param string $name
     *
     * @return Rubric
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
     * Set description
     *
     * @param string $description
     *
     * @return Rubric
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Rubric
     */
    protected function setCreatedAt($createdAt)
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
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Rubric
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
     * Set specialAccess
     *
     * @param integer $specialAccess
     *
     * @return Rubric
     */
    public function setSpecialAccess($specialAccess)
    {
        $this->special_access = $specialAccess;

        return $this;
    }

    /**
     * Get specialAccess
     *
     * @return integer
     */
    public function getSpecialAccess()
    {
        return $this->special_access;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Rubric
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
     * Set mainImg
     *
     * @param string $mainImg
     *
     * @return Rubric
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
     * Set parent
     *
     * @param array $parent
     *
     * @return Rubric
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Rubric|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set children
     *
     * @param string $children
     *
     * @return Rubric
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return string
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Rubric $child
     *
     * @return Rubric
     */
    public function addChild(\AppBundle\Entity\Rubric $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Rubric $child
     */
    public function removeChild(\AppBundle\Entity\Rubric $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function beforeSaving()
    {
        if(empty($this->getCreatedAt())){
            $this->setCreatedAt(new \DateTime());
        }
    }

    /**
     * Add article
     *
     * @param \AppBundle\Entity\Article $article
     *
     * @return Rubric
     */
    public function addArticle(\AppBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \AppBundle\Entity\Article $article
     */
    public function removeArticle(\AppBundle\Entity\Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Rubric
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
}
