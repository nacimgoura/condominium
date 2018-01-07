<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sondage
 *
 * @ORM\Table(name="sondage")
 * @ORM\Entity(repositoryClass="App\Repository\SondageRepository")
 */
class Sondage
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
     * @ORM\Column(name="question", type="string", length=255)
     */
    private $question;

    /**
     * @var array
     * @Assert\NotBlank()
     * @ORM\Column(name="choice", type="array")
     */
    private $choice;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="sondage", cascade={"all"})
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="sondage")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=true)
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sondage")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion(string $question)
    {
        $this->question = $question;
    }

    /**
     * @return array
     */
    public function getChoice()
    {
        return $this->choice;
    }

    /**
     * @param array $choice
     */
    public function setChoice(array $choice)
    {
        $this->choice = $choice;
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
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

}