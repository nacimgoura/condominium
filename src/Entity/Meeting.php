<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MeetingRepository")
 */
class Meeting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
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
     * @ORM\OneToOne(targetEntity="Sondage")
     * @ORM\JoinColumn(name="sondage_id", referencedColumnName="id", nullable=true)
     */
    private $sondage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Condominium", inversedBy="meeting")
     * @ORM\JoinColumn(name="condominium_id", referencedColumnName="id")
     */
    private $condominium;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return mixed
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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
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


}
