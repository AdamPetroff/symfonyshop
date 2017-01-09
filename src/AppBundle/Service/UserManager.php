<?php
/**
 * Created by Adam The Great.
 * Date: 7. 1. 2017
 * Time: 2:25
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;
use AppBundle\Repository\AdminRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Tests\Encoder\PasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
    /**
     * @var AdminRepository
     */
    protected $repository;
    /**
     * @var PasswordEncoder
     */
    private $encoder;

    public function __construct(Registry $doctrine, UserPasswordEncoder $encoder)
    {
        $this->repository = $doctrine->getRepository(User::class);
        $this->encoder = $encoder;
    }

    /**
     * @param User $user
     */
    public function save(User $user)
    {
        $this->repository->saveUser($user);
    }

    /**
     * @param User $user
     */
    public function saveNew(User $user)
    {
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        $this->repository->saveUser($user);
    }

    /**
     * @param string $username
     * @return User
     */
    public function findByUsername(string $username)
    {
        return $this->repository->findOneBy(['username' => $username]);
    }

    /**
     * @param UserInterface $admin
     * @param string $enteredPassword
     * @return bool
     */
    public function checkPassword(UserInterface $admin, string $enteredPassword)
    {
        return $this->encoder->isPasswordValid($admin, $enteredPassword);
    }
}