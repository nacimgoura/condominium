<?php

namespace App\DataFixtures;

use App\Service\FakerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Conversation;
use App\Entity\Message;

class ForumFixture extends Fixture implements OrderedFixtureInterface
{

    private $faker;

    public function __construct(FakerService $faker)
    {
        $this->faker = $faker;
    }
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @throws \Doctrine\Common\DataFixtures\BadMethodCallException
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i <= 10; $i++) {
            $conversation = new Conversation();
            $conversation->setTitle($this->faker->generate()->sentence);
            $conversation->setDescription($this->faker->generate()->text);
            $conversation->setContent($this->faker->generate()->text(2500));
            $conversation->setTags(['test', 'genial', 'facture']);
            $conversation->setUser($this->getReference('user-fixture'.$i));
            if ($i <= 5) {
                $conversation->setCondominium($this->getReference('condominium-fixture1'));
            } else {
                $conversation->setCondominium($this->getReference('condominium-fixture2'));
            }

            $listUser = [];
            for ($j = 0; $j <= 5; $j++) {
                if ($i < 3) {
                    array_push($listUser, $this->getReference('user-fixture'.$j));
                }
                $message = new Message();
                $message->setContent($this->faker->generate()->text);
                $message->setUser($this->getReference('user-fixture'.$j));
                $message->setConversation($conversation);
                $manager->persist($message);
            }
            $conversation->setAuthorizedUser($listUser);
            $manager->persist($conversation);
        }

        $conversationProject = new Conversation();
        $conversationProject->setTitle('Achat d\'une nouvelle poubelle ?');
        $conversationProject->setDescription($this->faker->generate()->text);
        $conversationProject->setContent($this->faker->generate()->text(2500));
        $conversationProject->setUser($this->getReference('user-manager-fixture'));
        $this->addReference('conversation-project-fixture', $conversationProject);
        $manager->persist($conversationProject);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 4;
    }
}