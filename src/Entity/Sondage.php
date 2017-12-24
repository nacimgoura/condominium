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
     * @ORM\Column(name="answer_available", type="array")
     * @ORM\OneToMany(targetEntity="Category", mappedBy="answer_given")
     */
    private $answerAvailable;

    /**
     * @var array
     * @Assert\NotBlank()
     * @ORM\Column(name="answer_given", type="array")
     * @ORM\ManyToOne(targetEntity="Sondage", inversedBy="answer_available")
     * @ORM\JoinColumn(name="answer_given_id", referencedColumnName="id")
     */
    private $answerGiven;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    /**
     * @return array
     */
    public function getAnswerAvailable(): array
    {
        return $this->answerAvailable;
    }

    /**
     * @param array $answerAvailable
     */
    public function setAnswerAvailable(array $answerAvailable): void
    {
        $this->answerAvailable = $answerAvailable;
    }

    /**
     * @return array
     */
    public function getAnswerGiven(): array
    {
        return $this->answerGiven;
    }

    /**
     * @param array $answerGiven
     */
    public function setAnswerGiven(array $answerGiven): void
    {
        $this->answerGiven = $answerGiven;
    }


}