<?php

namespace App\Repository;

use App\Entity\Subscribe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subscribe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscribe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscribe[]    findAll()
 * @method Subscribe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscribeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscribe::class);
    }

    public function getNextSubscribe(Subscribe $subscribe)
    {
        $subQuery = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.code = :code')
            ->setParameter('code', $subscribe->getCode())
            ->getQuery()
            ->getSingleResult()
        ;

        $query = $this->createQueryBuilder('s')
            ->where('s.id > :id')
            ->setParameter('id', $subQuery->getId())
            ->getQuery()
            ->setMaxResults(1)
        ;

        return count($query->getResult()) > 0 ? $query->getSingleResult() : null;

    }

}
