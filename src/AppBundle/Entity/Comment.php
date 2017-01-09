<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 * @ORM\Table(name="comment")
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $text;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Article", inversedBy="comments")
     * @var Article
     */
    protected $article;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $postedBy;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CommentVote", mappedBy="comment", cascade={"remove"})
     * @var CommentVote[]
     */
    protected $votes;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comment", inversedBy="children")
     * @var Comment
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="parent", cascade={"remove"})
     * @var Comment[]
     */
    protected $children;
    

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text) 
    {
        $this->text = $text;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getPostedBy(): ?string
    {
        return $this->postedBy;
    }

    /**
     * @param mixed $postedBy
     */
    public function setPostedBy($postedBy)
    {
        $this->postedBy = $postedBy;
    }

    /**
     * @return CommentVote[]|Collection
     */
    public function getVotes(): ?Collection
    {
        return $this->votes;
    }

    /**
     * @param CommentVote[] $votes
     */
    public function setVotes(array $votes)
    {
        $this->votes = $votes;
    }

    /**
     * @return int
     */
    public function getVoting() : int
    {
        $voting = 0;
        foreach ($this->getVotes() ?? [] as $vote) {
            if($vote->getReaction()){
                $voting ++;
            }
            else{
                $voting --;
            }
        }
        return $voting;
    }

    /**
     * @return Article
     */
    public function getArticle() : ?Article
    {
        return $this->article;
    }

    /**
     * @param Article $article
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @return Comment
     */
    public function getParent(): ?Comment
    {
        return $this->parent;
    }

    /**
     * @param Comment $parent
     */
    public function setParent(Comment $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return Comment[]|Collection
     */
    public function getChildren(): ?Collection
    {
        return $this->children;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children)
    {
        $this->children = $children;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
    }
}