<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
/**
 * ConversationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConversationRepository extends EntityRepository
{
    /**
     * return all data if not archived and visible by user
     * @param $user
     * @return array
     */
    public function findAllAuthorized($user) {
        if ($user->getUsername() == 'admin') {
            return $this->createQueryBuilder('c')
                ->where('c.isArchived = false')
                ->orderBy('c.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }
        return $this->createQueryBuilder('c')
            ->join('c.authorizedUser', 'au')
            ->where('c.isArchived = false AND au.id = :id')
            ->setParameter('id', $user->getId())
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
