<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Admin
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdminRepository")
 * @ORM\Table(name="admins")
 * @ORM\HasLifecycleCallbacks()
 */
class Admin implements \Serializable, UserInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(unique=true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column()
     * @Assert\Length(min="6", minMessage="The password has to be at least 6 characters long")
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(unique=true)
     */
    protected $email;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $registrationDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $locked;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    protected $roles;

    public function __construct(){
        $this->setIsActive(true);
        $this->setLocked(false);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Admin
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Admin
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Admin
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     * @return Admin
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return Admin
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Admin
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return Admin
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return Admin
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    public function eraseCredentials()
    {
        
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setRegistrationDate(new \DateTime());
    }

}
