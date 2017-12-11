<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * @param string $username
     * @return mixed|null|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllExceptAdmin() {
        return $this->createQueryBuilder('u')
            ->where('u.username != :admin')
            ->setParameter('admin', 'admin')
            ->getQuery()
            ->getResult();
    }

    public function findAllExceptAdminAndUs($username) {
        return $this->createQueryBuilder('u')
            ->where('u.username != :admin AND u.username != :us')
            ->setParameter('admin', 'admin')
            ->setParameter('us', $username)
            ->getQuery()
            ->getResult();
    }
}