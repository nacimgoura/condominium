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
     * @return array
     */
    public function generate(Charge $charge) {
        $listUser = $charge->getAuthorizedUser();
        $listPayment = [];

        foreach($listUser as $user) {
            $payment = new Payment();
            $payment->setUser($user);
            $payment->setAmountTotal($charge->getAmount() / count($listUser));
            $payment->setAmountPaid(0);
            $payment->setType('VIREMENT BANCAIRE');
            $payment->setCharge($charge);
            array_push($listPayment, $payment);
        }
        return $listPayment;
    }
}