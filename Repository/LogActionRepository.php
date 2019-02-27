<?php

namespace LoremIpsum\ActionLoggerBundle\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use LoremIpsum\ActionLoggerBundle\Factory\ActionFactory;
use LoremIpsum\ActionLoggerBundle\Entity\LogAction;
use LoremIpsum\ActionLoggerBundle\Entity\LogActionRelation;

class LogActionRepository extends ServiceEntityRepository
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    public function __construct(ManagerRegistry $registry, ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
        parent::__construct($registry, LogAction::class);
    }

    /**
     * @param User  $user
     * @param array $actions List of action names
     * @param array $relations List of Entity class => [ids]
     * @param int   $offset
     * @param int   $limit 0 for no limit
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
            $this->filterRelations($qb, $relations);
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

    private function filterRelations(QueryBuilder $qb, $relations)
    {
        $relationList = [];
        foreach ($relations as $entityClass => $ids) {
            $keyEntity = $this->actionFactory->getEntityKey($entityClass);
            foreach ((array)$ids as $id) {
                $relationList[] = LogActionRelation::hash($id, $keyEntity);
            }
        }
        if (! empty($relationList)) {
            $qb->leftJoin('log.relations', 'relations')
               ->andWhere("relations.keyHash IN (:relationList)")
               ->setParameter('relationList', $relationList);
        }
    }
}
