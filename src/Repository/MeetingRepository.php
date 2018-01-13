<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class MeetingRepository extends EntityRepository
{

    public function findOwnMeeting(User $user) {
        if ($user->getUsername() == 'admin') {
            return $this->createQueryBuilder('c')
                ->getQuery()
                ->getResult();
        } else if (in_array('ROLE_MANAGER', $user->getRoles())) {
            return $this->createQueryBuilder('c')
                ->where('c.condominium = :condominium')
                ->setParameter('condominium', $user->getCondominium()->getId())
                ->getQuery()
                ->getResult();
        }
        return $this->createQueryBuilder('c')
            ->where('c.condominium = :condominium')
            ->setParameter('condominium', $user->getCondominium()->getId())
            ->getQuery()
            ->getResult();
    }
}
