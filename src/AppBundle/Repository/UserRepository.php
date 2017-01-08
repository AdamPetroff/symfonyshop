<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    public function saveUser(User $admin)
    {
        $this->getEntityManager()->persist($admin);
        $this->getEntityManager()->flush();
    }
}
