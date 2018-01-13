<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Condominium
 *
 * @ORM\Table(name="condominium")
 * @ORM\Entity(repositoryClass="App\Repository\CondominiumRepository")
 * @UniqueEntity("title")
 */
class Condominium
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
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id", nullable=true, onDelete="SET NULL", unique=true)
     */
    private $manager;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="condominium", cascade={"all"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Sondage", mappedBy="condominium", cascade={"all"})
     */
    private $sondage;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="condominium", cascade={"all"})
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="Conversation", mappedBy="condominium", cascade={"all"})
     */
    private $conversation;

    /**
     * @ORM\OneToMany(targetEntity="Charge", mappedBy="condominium", cascade={"all"})
     */
    private $charge;

    /**
     * @ORM\OneToMany(targetEntity="Contract", mappedBy="condominium", cascade={"all"})
     */
    private $contract;

    /**
     * @ORM\OneToMany(targetEntity="Meeting", mappedBy="condominium", cascade={"all"})
     */
    private $meeting;


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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param mixed $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
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


    public function __toString()
    {
        return $this->title;
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
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * @param mixed $charge
     */
    public function setCharge($charge)
    {
        $this->charge = $charge;
    }

    /**
     * @return mixed
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @param mixed $contract
     */
    public function setContract($contract)
    {
        $this->contract = $contract;
    }

    /**
     * @return mixed
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * @param mixed $meeting
     */
    public function setMeeting($meeting)
    {
        $this->meeting = $meeting;
    }
}

