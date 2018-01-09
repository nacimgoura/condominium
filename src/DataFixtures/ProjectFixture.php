<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Service\FakerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProjectFixture extends Fixture implements OrderedFixtureInterface
{
    private $faker;

    public function __construct(FakerService $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Load user
     *
     * @param ObjectManager $manager
     * @throws \Doctrine\Common\DataFixtures\BadMethodCallException
     */
    public function load(ObjectManager $manager)
    {
        $project = new Project();
        $project->setTitle($this->faker->generate()->text(15));
        $project->setDescription($this->faker->generate()->text());
        $project->setStatus('EN DISCUSSION');
        $project->setDeadline($this->faker->generate()->dateTimeThisYear);
        $project->setConversation($this->getReference('conversation-project-fixture'));
        $project->setUser($this->getReference('user-manager-fixture'));
        $project->setCondominium($this->getReference('condominium-fixture1'));
        $listUserAuthorized = [$project->getUser()];
        for ($i = 0; $i <= 5; $i++) {
            array_push($listUserAuthorized, $this->getReference('user-fixture'.$i));
        }
        $project->setAuthorizedUser($listUserAuthorized);

        $this->addReference('project-fixture', $project);

        $manager->persist($project);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 6;
    }
}