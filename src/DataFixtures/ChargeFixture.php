<?php

namespace App\DataFixtures;

use App\Entity\Charge;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class ChargeFixture extends Fixture
{
    private $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        for ($i = 0; $i < 10; $i++) {
            $charge = new Charge();
            $charge->setName('product '.$i);
            $charge->setAmount(mt_rand(10, 1000));
            $charge->setDeadline($this->faker->dateTime());
            $manager->persist($charge);
        }
    }
}