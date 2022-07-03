<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 *
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Module $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Module $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param $userId
     * @return array|null
     */
    public function getUserModules($userId): ?array
    {
        return $this->createQueryBuilder('m')
            ->where('m.user = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getUserModulesContents($userId): ?array
    {
        return $this->createQueryBuilder('m')
            ->select('m.content')
            ->where('m.user = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getResult()
        ;
    }
}
