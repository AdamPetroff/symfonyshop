<?php
/**
 * Created by Adam The Great.
 * Date: 23. 12. 2016
 * Time: 21:31
 */

namespace AppBundle\Service;


use AppBundle\Entity\Admin;
use AppBundle\Repository\AdminRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminManager
{
    /**
     * @var AdminRepository
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
        $this->repository = $doctrine->getRepository(Admin::class);
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    /**
     * @param string $username
     * @return Admin
     */
    public function findByUsername(string $username): ?Admin
    {
        return $this->repository->findOneBy(['username' => $username]);
    }

    /**
     * @param UserInterface $admin
     * @param string $enteredPassword
     * @return bool
     */
    public function checkPassword(UserInterface $admin, string $enteredPassword): bool
    {
        return $this->encoder->isPasswordValid($admin, $enteredPassword);
    }

    /**
     * @param Admin $admin
     * @return string
     */
    public function assignNewPassword(Admin $admin) : string
    {
        $newPassword = substr(md5(rand()), 0, 7);
        $admin->setPassword($this->encoder->encodePassword($admin, $newPassword));
        $this->repository->saveUser($admin);

        return $newPassword;
    }

    /**
     * @return Admin[]|array
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param Admin $admin
     */
    public function save(Admin $admin)
    {
        $this->repository->saveUser($admin);
    }

    /**
     * @param Admin $admin
     */
    public function saveNew(Admin $admin)
    {
        $admin->setPassword($this->encoder->encodePassword($admin, $admin->getPassword()));
        $this->repository->saveUser($admin);
    }
}