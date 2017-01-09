<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Rubric
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="APpBundle\Repository\RubricRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @var mixed
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rubric", inversedBy="children")
     */
    protected $parent;

    /**
     * @var Collection|Rubric[]
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
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription(string $description) 
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    protected function setCreatedAt(\DateTime $createdAt) 
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     */
    public function setDeleted(bool $deleted) 
    {
        $this->deleted = $deleted;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * Set specialAccess
     *
     * @param integer $specialAccess
     */
    public function setSpecialAccess(int $specialAccess)
    {
        $this->special_access = $specialAccess;
    }

    /**
     * Get specialAccess
     *
     * @return integer
     */
    public function getSpecialAccess(): int
    {
        return $this->special_access;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl(string $url) 
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set parent
     *
     * @param array $parent
     */
    public function setParent($parent) 
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Rubric|null
     */
    public function getParent(): ?Rubric
    {
        return $this->parent;
    }

    /**
     * Set children
     *
     * @param array $children
     */
    public function setChildren(array $children) 
    {
        $this->children = $children;
    }

    /**
     * Get children
     *
     * @return Collection|Rubric[]
     */
    public function getChildren(): ?Collection
    {
        return $this->children;
    }

    /**
     * @return Collection|null
     */
    public function getNonDeletedChildren(): ?Collection
    {
        if(!empty($this->getChildren())) {
            return $this->getChildren()->filter(function (Rubric $rubric) {
                if ($rubric->getDeleted()) {
                    return false;
                } else {
                    return true;
                }
            });
        } else {
            return null;
        }
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
     * Get articles
     *
     * @return Collection
     */
    public function getArticles(): ?Collection
    {
        if(!empty($this->articles)) {
            return $this->articles->filter(function(Article $article){
                return !$article->getDeleted();
            });
        } else {
            return null;
        }
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive(bool $active) 
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive(): bool
    {
        return $this->active;
    }
}
