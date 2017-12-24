<?php

namespace App\DataFixtures;

use App\Entity\Sondage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SondageFixture extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sondage = new Sondage();
        $sondage->setQuestion('Ca va ?');
        $sondage->setAnswerAvailable(['oui', 'non']);
        $manager->persist($sondage);
        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}