<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Payment
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="payment")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_paid", type="float", scale=2)
     */
    private $amountPaid;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_total", type="float", scale=2)
     */
    private $amountTotal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Charge", inversedBy="payment")
     * @ORM\JoinColumn(name="charge_id", referencedColumnName="id")
     */
    private $charge;

    public function __construct()
    {
        $this->listAttachment = [];
        $this->createdAt = new \DateTime();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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
     * @return array
     */
    public function getListAttachment()
    {
        return $this->listAttachment;
    }

    /**
     * @param array $listAttachment
     */
    public function setListAttachment(array $listAttachment)
    {
        $this->listAttachment = $listAttachment;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    /**
     * @param float $amountPaid
     */
    public function setAmountPaid(float $amountPaid)
    {
        $this->amountPaid = $amountPaid;
    }

    /**
     * @return float
     */
    public function getAmountTotal()
    {
        return $this->amountTotal;
    }

    /**
     * @param float $amountTotal
     */
    public function setAmountTotal(float $amountTotal)
    {
        $this->amountTotal = $amountTotal;
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
     * @ORM\PostRemove
     */
    public function deleteAttachment() {
        try {
            $fs = new Filesystem();
            foreach($this->listAttachment as $attachment) {
                $fs->remove('attachment/'.$attachment);
            }
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }
    }
}

