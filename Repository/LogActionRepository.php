<?php

namespace LoremIpsum\ActionLoggerBundle\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class LogActionRepository extends EntityRepository
{
    /**
     * @param User  $user
     * @param array $actions   List of action names
     * @param array $relations List of Entity class => [ids]
     * @param int   $offset
     * @param int   $limit     0 for no limit
     *
     * @return Paginator
     */
    public function findLogs(
        User $user = null,
        array $actions = [],
        array $relations = [],
        int $offset = 0,
        int $limit = 0
    ) {
        $qb = $this->createQueryBuilder('log');

        if ($user) {
            $qb->andWhere('log.user = :user')->setParameter('user', $user);
        }

        if (! empty($actions)) {
            $qb->andWhere('log.action IN (:actions)')->setParameter('actions', $actions);
        }

        if (! empty($relations)) {
            $relationList = [];
            foreach ($relations as $entity => $ids) {
                foreach ((array)$ids as $id) {
                    $relationList[] = $id . ':' . $entity;
                }
            }
            if (! empty($relationList)) {
                $qb->leftJoin('log.relations', 'relations');
                $qb->andWhere("CONCAT(relations.keyId, ':', relations.keyEntity) IN (:relationList)");
                $qb->setParameter('relationList', $relationList);
            }
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }

        $qb->orderBy('log.id', 'DESC');

        return new Paginator($qb);
    }
}
