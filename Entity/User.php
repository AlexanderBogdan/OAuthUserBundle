<?php

namespace Obogdan\OAuthUserBundle\Entity;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Obogdan\OAuthUserBundle\Repository\UserRepository")
 */
class User implements UserInterface , EquatableInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $last_name;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=32)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    protected $role;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login_date", type="datetime")
     */
    protected $last_login_date;


    public function __construct($email)
    {
        $this->email = $email;
        $this->setUsername($email);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set role
     *
     * @param $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set first name
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set first name
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set last login date
     *
     * @param \DateTime $date
     *
     * @return User
     */
    public function setLastLoginDate(\DateTime $date) {
        $this->last_login_date = $date;
        return $this;
    }

    /**
     * Get last login date
     *
     * @return \DateTime
     */
    public function getLastLoginDate() {
        return $this->last_login_date;
    }

    /**
     * Equatable
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if ($user->getUsername() == $this->getUsername()) {
            return true;
        }

        return false;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
        ]);
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username
            ) = unserialize($serialized);
    }

    public function getRoles()
    {
        return [$this->getRole()];
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        return true;
    }
}

