<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class SondageRepository extends EntityRepository
{
    public function findOwnSondage(User $user) {
        if ($user->getUsername() == 'admin') {
            return $this->createQueryBuilder('s')
                ->getQuery()
                ->getResult();
        }
        return $this->createQueryBuilder('s')
            ->where('s.condominium = :condominium')
            ->setParameter('condominium', $user->getCondominium()->getId())
            ->getQuery()
            ->getResult();
    }
}