<?php

namespace App\Service;

use App\Entity\Charge;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

class PaymentService
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * generates a charge payment for each user
     * @param Charge $charge
     */
    public function generate(Charge $charge) {


        $listUser = $charge->getUser();
        foreach($listUser as $user) {
            $payment = new Payment();
            $payment->setUser($user);
            $payment->setAmountTotal($charge->getAmount() / count($listUser));
            $payment->setAmountPaid(0);
            $payment->setType('VIREMENT BANCAIRE');
            $payment->setCharge($charge);
            $this->em->persist($payment);
        }
    }
}