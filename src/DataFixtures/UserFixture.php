<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\FakerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture implements OrderedFixtureInterface
{

    private $encoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder, FakerService $faker)
    {
        $this->encoder = $encoder;
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
        // create admin
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@gmail.com');
        $admin->setFirstname('admin');
        $admin->setLastname('admin');
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MANAGER']);
        $admin->setPassword($this->encoder->encodePassword($admin, 'admin'));
        $manager->persist($admin);

        // create first user
        $user_manager = new User();
        $user_manager->setUsername('nacim');
        $user_manager->setEmail('nacim.goura@gmail.com');
        $user_manager->setFirstname('Nacim');
        $user_manager->setLastname('Goura');
        $user_manager->setPassword($this->encoder->encodePassword($user_manager, 'nacim'));
        $user_manager->setRoles(['ROLE_USER', 'ROLE_MANAGER']);
        $manager->persist($user_manager);

        $this->addReference('user-fixture-manager', $user_manager);

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($this->faker->generate()->userName);
            $user->setEmail($this->faker->generate()->email);
            $user->setFirstname($this->faker->generate()->firstName);
            $user->setLastname($this->faker->generate()->lastName);
            $user->setPassword($this->encoder->encodePassword($user, 'nacim'));
            $user->setRoles(['ROLE_USER']);
            $this->addReference('user-fixture'.$i, $user);
            if ($i <= 5) {
                $user->setCondominium($this->getReference('condominium-fixture1'));
            }
            if ($i > 5) {
                $user->setCondominium($this->getReference('condominium-fixture2'));
            }
            $manager->persist($user);
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
        return 2;
    }
}