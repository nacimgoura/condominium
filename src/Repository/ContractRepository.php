<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
/**
 * ContractRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContractRepository extends EntityRepository
{
    public function findOwnContract(User $user) {
        if ($user->getUsername() == 'admin') {
            return $this->createQueryBuilder('c')
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
