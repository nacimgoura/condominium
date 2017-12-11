<?php

namespace App\DataFixtures;

use App\Entity\Condominium;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CondominiumFixture extends Fixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @throws \Doctrine\Common\DataFixtures\BadMethodCallException
     */
    public function load(ObjectManager $manager)
    {
        $condominium = new Condominium();
        $condominium->setTitle('Elysée');
        $manager->persist($condominium);
        $this->addReference('condominium-fixture1', $condominium);

        $condominium = new Condominium();
        $condominium->setTitle('Taj Mahal');
        $manager->persist($condominium);
        $this->addReference('condominium-fixture2', $condominium);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}