<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Load user
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // create admin
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@gmail.com');
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setPassword($this->encoder->encodePassword($user, 'admin'));
        $manager->persist($user);

        // create first user
        $user = new User();
        $user->setUsername('nacim');
        $user->setEmail('nacim.goura@gmail.com');
        $user->setPassword($this->encoder->encodePassword($user, 'nacim'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $manager->flush();
    }
}