<?php
/**
 * Created by Adam The Great.
 * Date: 23. 12. 2016
 * Time: 21:31
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class AdminManager
{
    /**
     * @var UserRepository
     */
    protected $repository;
    /**
     * @var Registry
     */
    private $doctrine;
    /**
     * @var UserPasswordEncoder
     */
    private $encoder;

    public function __construct(Registry $doctrine, UserPasswordEncoder $encoder)
    {
        $this->repository = $doctrine->getRepository(User::class);
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    public function findByUsername(string $username)
    {
        return $this->repository->findOneBy(['username' => $username]);
    }

    public function assignNewPassword(User $user) : string
    {
        $new_password = substr(md5(rand()), 0, 7);
        $user->setPassword($this->encoder->encodePassword($user, $new_password));
        $this->repository->flush($user);
        
        return $new_password;
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function save(User $user)
    {
        $this->repository->flush($user);
    }

    public function saveNew(User $user)
    {
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        $this->repository->flush($user);
    }
}