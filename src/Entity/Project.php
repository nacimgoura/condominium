<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @UniqueEntity("title")
 */
class Project
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
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="content", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * @var string
     * @Assert\File(
     *     maxSize = "2048k"
     * )
     * @ORM\Column(name="attachment", type="string", nullable=true)
     */
    private $attachment;

    /**
     * @var array
     * @ORM\Column(name="listAttachment", type="array", nullable=true)
     */
    private $listAttachment;

    /**
     * @ORM\OneToOne(targetEntity="Conversation")
     * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id", nullable=true)
     */
    private $conversation;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="project")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * JoinTable(name="user",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="authorized_user", referencedColumnName="id")}
     *      )
     */
    private $authorizedUser;

    /**
     * @ORM\OneToMany(targetEntity="Sondage", mappedBy="project")
     * @ORM\JoinColumn(name="sondage_id", referencedColumnName="id", nullable=true)
     */
    private $sondage;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * @param \DateTime $deadline
     */
    public function setDeadline(\DateTime $deadline)
    {
        $this->deadline = $deadline;
    }

    /**
     * @return string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param string $attachment
     */
    public function setAttachment(string $attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @return array
     */
    public function getListAttachment(): array
    {
        return $this->listAttachment;
    }

    /**
     * @param array $listAttachment
     */
    public function setListAttachment(array $listAttachment): void
    {
        $this->listAttachment = $listAttachment;
    }

    /**
     * @return mixed
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * @param mixed $conversation
     */
    public function setConversation($conversation)
    {
        $this->conversation = $conversation;
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


}