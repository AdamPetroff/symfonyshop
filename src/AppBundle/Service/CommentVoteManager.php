<?php
/**
 * Created by Adam The Great.
 * Date: 8. 1. 2017
 * Time: 21:49
 */

namespace AppBundle\Service;


use AppBundle\Entity\Comment;
use AppBundle\Entity\CommentVote;
use AppBundle\Entity\User;
use AppBundle\Repository\CommentVoteRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;

class CommentVoteManager
{
    /**
     * @var CommentVoteRepository
     */
    protected $repository;
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->repository = $doctrine->getRepository(CommentVote::class);
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function hasUserVotedOnComment(User $user, Comment $comment) : bool
    {
        if (!empty($this->repository->findByUserAndComment($user, $comment))) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @param bool $reaction
     * @return Comment
     */
    public function voteOnComment(User $user, Comment $comment, bool $reaction)
    {
        $commentVote = new CommentVote();
        $commentVote->setUser($user);
        $commentVote->setComment($comment);
        $commentVote->setReaction($reaction);
        $this->repository->saveCommentVote($commentVote);
    }

}