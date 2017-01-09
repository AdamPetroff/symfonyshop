<?php
/**
 * Created by Adam The Great.
 * Date: 23. 12. 2016
 * Time: 18:04
 */

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Repository\ArticleRepository;
use AppBundle\Utils\Strings;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Stringy\StaticStringy;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleManager
{
    /**
     * @var ArticleRepository
     */
    protected $repository;

    public function __construct(Registry $doctrine)
    {
        $this->repository = $doctrine->getRepository(Article::class);
    }

    /**
     * @param Article $article
     */
    public function saveArticle(Article $article)
    {
        $this->assignUniqueUrl($article);
        $this->saveMainImageName($article);
        $this->repository->saveArticle($article);
        $this->saveMainImageFile($article);
    }

    /**
     * @param Article $article
     */
    public function assignUniqueUrl(Article $article)
    {
        if (!empty($article->getUrl())) {
            return;
        }
        $article->setUrl(StaticStringy::slugify($article->getName()));

        while (!$this->repository->isArticleUrlUnique($article)) {
            $article->setUrl(Strings::incrementDoubleDashUrl($article->getUrl()));
        }
    }

    /**
     * @param Article $entity
     */
    public function saveMainImageName(Article $entity)
    {
        if ($entity->getTmpMainImgFile() instanceof UploadedFile) {
            /** @var UploadedFile $file */
            $file = $entity->getTmpMainImgFile();
            $newName = md5(uniqid()) . '.' . $file->guessExtension();
            $entity->setMainImg($newName);
        }
    }

    /**
     * @param Article $entity
     */
    public function saveMainImageFile(Article $entity)
    {
        if ($entity->getTmpMainImgFile() instanceof UploadedFile) {
            /** @var UploadedFile $mainImg */
            $mainImg = $entity->getTmpMainImgFile();
            $newName = $entity->getMainImg();
            $path = WEB_DIR . $entity->getImageDir();
            if (!is_dir($path)) {
                mkdir($path);
            }
            $mainImg->move($path, $newName);
        }
    }

    /**
     * @param Article $article
     */
    public function deleteArticle(Article $article)
    {
        $article->setDeleted(true);
        $this->repository->saveArticle($article);
    }

    /**
     * @param int $articleId
     * @return Article
     */
    public function getArticle(int $articleId) : Article
    {
        $article = $this->repository->find($articleId);

        if (!$article) {
            return null;
        }
        return $article;
    }

    /**
     * @return Article[]
     */
    public function getNonDeletedArticles() : array
    {
        return $this->repository->findNonDeleted();
    }
}