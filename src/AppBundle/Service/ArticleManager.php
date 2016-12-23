<?php
/**
 * Created by Adam The Great.
 * Date: 23. 12. 2016
 * Time: 18:04
 */

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Repository\ArticleRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;

class ArticleManager
{
    /**
     * @var ArticleRepository
     */
    protected $repository;
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->repository = $doctrine->getRepository(Article::class);
        $this->doctrine = $doctrine;
    }

    public function save(Article $article)
    {
        $this->repository->flush($article);
    }

    public function delete(Article $article)
    {
        $article->setDeleted(true);
        $this->repository->flush($article);
    }
}