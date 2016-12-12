<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Article;
use AppBundle\Entity\Rubric;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Stringy\StaticStringy;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * BaseRepository
 */
abstract class BaseRepository extends \Doctrine\ORM\EntityRepository
{
    public function query(string $alias = null, array $query, $index_by = null)
    {
        $qb = $this->createQueryBuilder($alias, $index_by);
        foreach($query as $name => $item){
            if(method_exists($qb, $name)){
                $qb->$name($item);
            }

        }
        
        return $qb->getQuery()->getResult();
    }

    public static function getKeyValue(array $array, $value_column, $index_by = null)
    {
        $result = [];

        foreach($array as $key => $value){
            if(($index_by && !isset($value[$index_by])) || !isset($value[$value_column])){
                return null;
            }
            $result[$index_by ? $value[$index_by] : $key] = $value[$value_column];
        }
        
        return $result;
    }

    public function persist($entity)
    {
        $this->preparePersist($entity);
        $this->getEntityManager()->persist($entity);
    }
    
    public function flush($entity = null){
        if(!empty($entity)){
            $this->persist($entity);
        }
        $this->getEm()->flush();

        if(!empty($entity)){
            $this->afterFlush($entity);
        }
    }

    public function getEm()
    {
        return $this->getEntityManager();
    }

    public function getOriginal($entity)
    {
        return $this->getEntityManager()->getUnitOfWork()->getOriginalEntityData($entity);
    }
    
    protected function preparePersist(&$entity){
        $this->getUniqueUrl($entity);
    }

    protected function afterFlush(&$entity){
        $this->saveImages($entity);
    }

    public function getUniqueUrl(&$entity) : bool
    {
        if(method_exists($entity, 'getName') && method_exists($entity, 'setUrl') && method_exists($entity, 'getId') && method_exists($entity, 'getUrl')){
            if(empty($entity->getUrl())){
                $url = StaticStringy::slugify($entity->getName());
            }
            elseif($this->getOriginal($entity)['url'] != $entity->getUrl()){
                $url = StaticStringy::slugify($entity->getUrl());
            }
            else{
                return false;
            }
            if(!empty($entity->getId())){
                $and = "AND a.id != {$entity->getId()}";
            }
            else{
                $and = '';
            }
            $count = count($this->query('a', ['where' => "a.url= '$url' $and"]));
            if($count > 0){
                $url .= '-' . ($count + 1);
            }
            $entity->setUrl($url);

            return true;
        }
        else{
            return false;
        }
    }

    public function saveImages(&$entity) : bool
    {
        if(method_exists($entity, 'getMainImg') && method_exists($entity, 'setMainImg') && method_exists($entity, 'getId') &&
            method_exists($entity, 'getImageDir') && $entity->getMainImg() instanceof UploadedFile){
            /** @var UploadedFile $main_img */
            $main_img = $entity->getMainImg();
            $new_name = md5(uniqid()) . '.' . $main_img->guessExtension();
            $path = WEB_DIR . $entity->getImageDir();
            if(!is_dir($path)){
                mkdir($path);
            }
            $main_img->move($path, $new_name);
            $entity->setMainImg($new_name);
            $this->getEm()->flush($entity);
            
            return true;
        }
        else{
            return false;
        }
    }
}