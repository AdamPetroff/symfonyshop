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
use Doctrine\Common\Collections\ArrayCollection;

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
     * @param int $id
     * @param bool $reaction
     * @return Comment
     */
    public function vote(int $id, bool $reaction)
    {
        //TODO - napojit na redis koli tomu ze viacero ludi moze hlasovat naraz
        $comment = $this->repository->find($id);
        if ($comment) {
            if ($reaction) {
                $comment->setVotes($comment->getVotes() + 1);
            } elseif (!$reaction) {
                $comment->setVotes($comment->getVotes() - 1);
            }
            $this->repository->flush($comment);
        }
        
        return $comment;
    }

    public function post(Comment $comment, $article_id = null)
    {
        $article = $this->doctrine->getRepository(Article::class)->find($_POST['article_id']);
        if(!$article) {
            return false;
        }
        $comment->setArticle($article);
        $this->repository->flush($comment);
        
        return $article;
    }

    public function delete(int $id)
    {
        $comment = $this->repository->find($_POST['id']);
        if(!$comment){
            return false;
        }
        $comment->setDeleted(true);
        $this->repository->flush($comment);
        
        return true;
    }

    /**
     * @param $article
     * @return ArrayCollection
     */
    public function findArticleCommentsOrderedByVotes($article)
    {
        $result = $this->repository->findArticleCommentsOrderedBy($article);
        return new ArrayCollection($result);
    }
    
}