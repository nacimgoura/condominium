<?php

namespace App\DataFixtures;

use App\Entity\Charge;
use App\Entity\Payment;
use App\Service\FakerService;
use App\Service\PaymentService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ChargeFixture extends Fixture implements OrderedFixtureInterface
{
    private $faker;
    private $paymentService;

    public function __construct(FakerService $faker, PaymentService $paymentService)
    {
        $this->faker = $faker;
        $this->paymentService = $paymentService;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 3; $i++) {
            $charge = new Charge();
            $charge->setTitle('charge '.$i);
            $charge->setAmount(mt_rand(10, 1000));
            $charge->setDeadline($this->faker->generate()->dateTimeThisMonth);
            $charge->setCondominium($this->getReference('condominium-fixture1'));

            $listUser = [];
            for ($j = 0 ; $j < 3 ; $j++) {
                array_push($listUser, $this->getReference('user-fixture'.$j));
            }
            $charge->setUser($listUser);
            $this->paymentService->generate($charge);
            $manager->persist($charge);
        }
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }
}