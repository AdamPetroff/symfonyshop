<?php

namespace AppBundle\Repository;

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
}