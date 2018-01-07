<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Charge
 *
 * @ORM\Table(name="charge")
 * @ORM\Entity(repositoryClass="App\Repository\ChargeRepository")
 * @UniqueEntity("title")
 * @ORM\HasLifecycleCallbacks()
 */
class Charge
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var float
     *
     * @Assert\Type("numeric")
     * @Assert\Range(
     *      min = 0
     * )
     * @ORM\Column(name="amount", type="float", scale=2)
     */
    private $amount;

    /**
     * @var \DateTime
     * @Assert\NotNull()
     * @ORM\Column(name="deadline", type="datetime")
     */
    private $deadline;

    /**
     * @Assert\NotNull()
     * @ORM\ManyToMany(targetEntity="User")
     * JoinTable(name="user",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $user;

    /**
     * @var string
     * @Assert\File(
     *     maxSize = "2048k"
     * )
     * @ORM\Column(name="attachment", type="string", length=255, nullable=true)
     */
    private $attachment;

    /**
     * @ORM\OneToOne(targetEntity="Contract")
     * @ORM\JoinColumn(name="contract_id", referencedColumnName="id", nullable=true)
     */
    private $contract;

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="charge", cascade={"all"})
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Condominium")
     * @ORM\JoinColumn(name="condominium_id", referencedColumnName="id")
     */
    private $condominium;



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
     * @param string title
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
     * Set amount
     *
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }

    /**
     * Get deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        $sumPayment = 0;
        if ($this->payment) {
            foreach ($this->payment as $item) {
                $sumPayment+= $item->getAmountPaid();
            }
            if (number_format($sumPayment) == $this->amount) {
                return 'Fini';
            }
        }
        return 'En cours';
    }

    /**
     * Set attachment
     *
     * @param string $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * Get attachment
     *
     * @return string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Set contract
     *
     * @param string $contract
     */
    public function setContract($contract)
    {
        $this->contract = $contract;
    }

    /**
     * Get contract
     *
     * @return string
     */
    public function getContract()
    {
        return $this->contract;
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

    public function __toString()
    {
        return $this->id.' - '.$this->title;
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteAttachment() {
        try {
            $fs = new Filesystem();
            $fs->remove('attachment/'.$this->attachment);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }
    }

}

