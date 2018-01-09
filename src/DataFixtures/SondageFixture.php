<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Sondage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SondageFixture extends Fixture implements OrderedFixtureInterface
{
    /**
     * Load user
     *
     * @param ObjectManager $manager
     * @throws \Doctrine\Common\DataFixtures\BadMethodCallException
     */
    public function load(ObjectManager $manager)
    {
        $sondage = new Sondage();
        $sondage->setQuestion('Devons nous acheter une nouvelle poubelle ?');
        $sondage->setChoice(['oui', 'non']);

        $answer1 = new Answer();
        $answer1->setTitle('oui');
        $answer1->setUser($this->getReference('user-fixture1'));
        $answer1->setSondage($sondage);

        $answer2 = new Answer();
        $answer2->setTitle('non');
        $answer2->setUser($this->getReference('user-fixture2'));
        $answer2->setSondage($sondage);

        $answer3 = new Answer();
        $answer3->setTitle('non');
        $answer3->setUser($this->getReference('user-fixture3'));
        $answer3->setSondage($sondage);

        $user = $this->getReference('user-manager-fixture');
        $sondage->setUser($user);
        $sondage->setProject($this->getReference('project-fixture'));
        $sondage->setCondominium($user->getCondominium());

        $manager->persist($answer1);
        $manager->persist($answer2);
        $manager->persist($answer3);
        $manager->persist($sondage);
        $manager->flush();
    }

    public function getOrder()
    {
        return 8;
    }
}