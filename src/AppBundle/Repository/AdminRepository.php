<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Admin;
use Doctrine\ORM\EntityRepository;

class AdminRepository extends EntityRepository
{
    public function saveUser(Admin $admin)
    {
        $this->getEntityManager()->persist($admin);
        $this->getEntityManager()->flush();
    }
}