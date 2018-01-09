<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface, \Serializable
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @Assert\Email()
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Condominium", inversedBy="user")
     * @ORM\JoinColumn(name="condominium_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $condominium;

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="user", cascade={"all"})
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id", nullable=true)
     */
    private $payment;

    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user", cascade={"all"})
     * @ORM\JoinColumn(name="notification_id", referencedColumnName="id", nullable=true)
     */
    private $notification;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="user", cascade={"all"})
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", nullable=true)
     */
    private $message;

    /**
     * @ORM\OneToMany(targetEntity="Conversation", mappedBy="user", cascade={"all"})
     * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id", nullable=true)
     */
    private $conversation;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="user", cascade={"all"})
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=true)
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="Sondage", mappedBy="user", cascade={"all"})
     * @ORM\JoinColumn(name="sondage_id", referencedColumnName="id", nullable=true)
     */
    private $sondage;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="user", cascade={"all"})
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id", nullable=true)
     */
    private $answer;

    /**
     * @ORM\Column(name="is_enabled", type="boolean")
     */
    private $isEnabled;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->createdAt = new \DateTime();
        $this->isEnabled = false;
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
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
     * @return mixed
     */
    public function getCondominium()
    {
        return $this->condominium;
    }

    /**
     * @param mixed $condominium
     */
    public function setCondominium($condominium)
    {
        $this->condominium = $condominium;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return mixed
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getSondage()
    {
        return $this->sondage;
    }

    /**
     * @param mixed $sondage
     */
    public function setSondage($sondage)
    {
        $this->sondage = $sondage;
    }

    /**
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param mixed $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return mixed
     */
    public function getisEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param mixed $isEnabled
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getCompleteName() {
        return $this->firstname.' '.$this->lastname;
    }

    public function __toString() {
        return $this->getCompleteName();
    }


}

