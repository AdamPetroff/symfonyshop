<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Rubric;

class RubricRepository extends BaseRepository
{
    public function handleAssociations(&$entity)
    {
        if(method_exists($entity, 'getParent') && method_exists($entity, 'setParent') && is_int($entity->getParent())){
            $entity->setParent($this->find($entity->getParent()));
        }
    }

    public function preparePersist(&$entity)
    {
        parent::preparePersist($entity);
        $this->handleAssociations($entity);
    }

    public function findProper(array $query = [])
    {
        $query['andWhere'] = 'r.deleted = 0 AND r.active = 1';
        return $this->query('r', $query);
    }

    public function findOtherThan(Rubric $entity)
    {
        $query = $this->createQueryBuilder('r')
            ->where('r.deleted = false');

        if($entity->getId()){
            $query
                ->andWhere('r.id != :rubric_id')
                ->setParameter('rubric_id', $entity->getId());
        }

        return $query
            ->getQuery()
            ->getResult();
    }
}