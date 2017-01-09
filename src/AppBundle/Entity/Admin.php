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
    protected $roles = [];

    public function __construct(){
        $this->setIsActive(true);
        $this->setLocked(false);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername(string $username) 
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername() : ?string
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword(string $password) 
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() : ?string
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail(string $email) 
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     */
    public function setRegistrationDate(\DateTime $registrationDate) 
    {
        $this->registrationDate = $registrationDate;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate() : ?\DateTime
    {
        return $this->registrationDate;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     */
    public function setLastLogin(\DateTime $lastLogin) 
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin() : ?\DateTime
    {
        return $this->lastLogin;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     */
    public function setIsActive(bool $isActive) 
    {
        $this->isActive = $isActive;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive() : bool
    {
        return $this->isActive;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     */
    public function setLocked(bool $locked) 
    {
        $this->locked = $locked;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked() : bool
    {
        return $this->locked;
    }

    /**
     * Set roles
     *
     * @param array $roles
     */
    public function setRoles($roles) 
    {
        $this->roles = $roles;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles() : array
    {
        return $this->roles;
    }
    
    public function eraseCredentials() 
    {
        
    }

    /**
     * @return string
     */
    public function serialize() : string
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized) 
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }

    /**
     * @return null
     */
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
