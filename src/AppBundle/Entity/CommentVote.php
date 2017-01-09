<?php
/**
 * Created by Adam The Great.
 * Date: 7. 1. 2017
 * Time: 18:33
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentVoteRepository")
 * @ORM\Table(name="comment_votes")
 */
class CommentVote
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(inversedBy="commentVotes", targetEntity="AppBundle\Entity\User")
     * @var User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comment", inversedBy="votes")
     * @var Comment
     */
    private $comment;

    /**
     * 1 is for positive, 0 for negative
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $reaction;

    /**
     * @return Comment
     */
    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    /**
     * @param Comment $comment
     */
    public function setComment(Comment $comment) 
    {
        $this->comment = $comment;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param bool $reaction
     */
    public function setReaction(bool $reaction)
    {
        $this->reaction = $reaction;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return boolean
     */
    public function getReaction() : ?bool
    {
        return $this->reaction;
    }
}