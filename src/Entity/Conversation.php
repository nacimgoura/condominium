<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Conversation
 *
 * @ORM\Table(name="conversation")
 * @ORM\Entity(repositoryClass="App\Repository\ConversationRepository")
 * @UniqueEntity("title")
 */
class Conversation
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
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="conversation")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "Vous devez choisir au moins un utilisateur"
     * )
     * @ORM\ManyToMany(targetEntity="User")
     * JoinTable(name="user",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="authorized_user", referencedColumnName="id")}
     *      )
     */
    private $authorizedUser;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="conversation", cascade={"all"})
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(name="tags", type="array", nullable=true)
     */
    private $tags;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_archived", type="boolean")
     */
    private $isArchived;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Condominium", inversedBy="conversation")
     * @ORM\JoinColumn(name="condominium_id", referencedColumnName="id", nullable=true)
     */
    private $condominium;

    /**
     * @ORM\OneToOne(targetEntity="Project", mappedBy="conversation")
     */
    private $project;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->isArchived = false;
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
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getAuthorizedUser()
    {
        return $this->authorizedUser;
    }

    /**
     * @param mixed $authorizedUser
     */
    public function setAuthorizedUser($authorizedUser)
    {
        $this->authorizedUser = $authorizedUser;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return bool
     */
    public function isArchived()
    {
        return $this->isArchived;
    }

    /**
     * @param bool $isArchived
     */
    public function setIsArchived(bool $isArchived)
    {
        $this->isArchived = $isArchived;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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

}
