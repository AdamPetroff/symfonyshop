<?php
/**
 * Created by Adam The Great.
 * Date: 23. 12. 2016
 * Time: 17:07
 */

namespace AppBundle\Service;


use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Repository\CommentRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;

class CommentManager
{
    /**
     * @var CommentRepository
     */
    protected $repository;
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->repository = $doctrine->getRepository(Comment::class);
        $this->doctrine = $doctrine;
    }

    /**
     * @param Comment $comment
     * @param Article $article
     */
    public function postComment(Comment $comment, Article $article)
    {
        $comment->setArticle($article);
        $this->repository->saveComment($comment);
    }

    /**
     * @param Comment $comment
     * @return bool
     */
    public function deleteComment(Comment $comment) : bool
    {
        try {
            $this->repository->deleteComment($comment);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @param int $commentId
     * @return Comment
     */
    public function getComment(int $commentId) : ?Comment
    {
        $comment = $this->repository->find($commentId);

        if (!$comment) {
            return null;
        }
        return $comment;
    }

    /**
     * @param Article $article
     * @return array
     */
    public function findArticleBaseCommentsOrderedByDate(Article $article) : array
    {
        $result = $this->repository->findArticleBaseCommentsOrderedByDate($article);
        return $result;
    }

}