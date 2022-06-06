<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Article find($id, $lockMode = null, $lockVersion = null)
 * @method null|Article findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    private function getUserArticlesQuery(int $userId)
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user_id')
            ->setParameter('user_id', $userId)
        ;
    }

    public function getUserArticles(int $userId)
    {
        $query = $this->getUserArticlesQuery($userId);

        return $query->getQuery()->getArrayResult();
    }

    public function getUserArticlesByPeriod(int $userId, Carbon $from, Carbon $to)
    {
        $query = $this->getUserArticlesQuery($userId);
        $query
            ->andWhere('a.created_at >= :from')
            ->setParameter('from', $from)
            ->andWhere('a.created_at <= :to')
            ->setParameter('to', $to)
        ;

        return $query->getQuery()->getArrayResult();
    }
}
