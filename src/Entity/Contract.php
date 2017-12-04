<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contract
 *
 * @ORM\Table(name="contract")
 * @ORM\Entity(repositoryClass="App\Repository\ContractRepository")
 */
class Contract
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
     * @var \DateTime
     *
     * @ORM\Column(name="signatureDate", type="date")
     */
    private $signatureDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="date")
     */
    private $deadline;

    /**
     * @ORM\OneToMany(targetEntity="Charge", mappedBy="contract")
     * @ORM\JoinColumn(name="charge_id", referencedColumnName="id")
     */
    private $charge;


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
     * Set signatureDate
     *
     * @param \DateTime $signatureDate
     *
     * @return Contract
     */
    public function setSignatureDate($signatureDate)
    {
        $this->signatureDate = $signatureDate;

        return $this;
    }

    /**
     * Get signatureDate
     *
     * @return \DateTime
     */
    public function getSignatureDate()
    {
        return $this->signatureDate;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return Contract
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
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


}

