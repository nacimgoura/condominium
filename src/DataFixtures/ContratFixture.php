<?php

namespace App\DataFixtures;

use App\Entity\Contract;
use App\Service\FakerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ContratFixture extends Fixture implements OrderedFixtureInterface
{
    private $faker;

    public function __construct(FakerService $faker)
    {
        $this->faker = $faker;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 3; $i++) {
            $contract = new Contract();
            $contract->setTitle($this->faker->generate()->colorName);
            $contract->setSignatureDate($this->faker->generate()->dateTimeThisDecade);
            $contract->setDeadline($this->faker->generate()->dateTimeBetween('+0 days', '+2 years'));
            $contract->setAttachment('example.png');
            $contract->setCondominium($this->getReference('condominium-fixture1'));
            $manager->persist($contract);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}