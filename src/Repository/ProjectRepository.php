<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    /**
     * return all project if visible by user
     * @param $user
     * @return array
     */
    public function findAllAuthorized($user) {
        if ($user->getUsername() == 'admin') {
            return $this->createQueryBuilder('p')
                ->orderBy('p.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        } else if (in_array('ROLE_MANAGER', $user->getRoles())) {
            return $this->createQueryBuilder('p')
                ->join('p.authorizedUser', 'au')
                ->where('au.id = :id OR p.user = :id AND p.condominium = :condominium')
                ->setParameter('id', $user->getId())
                ->setParameter('condominium', $user->getCondominium()->getId())
                ->orderBy('p.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }
        return $this->createQueryBuilder('p')
            ->join('p.authorizedUser', 'au')
            ->where('au.id = :id OR p.user = :id AND p.condominium = :condominium AND p.deadline < :today')
            ->setParameter('id', $user->getId())
            ->setParameter('condominium', $user->getCondominium()->getId())
            ->setParameter('today', new \DateTime())
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}