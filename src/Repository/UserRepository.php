<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method null|User find($id, $lockMode = null, $lockVersion = null)
 * @method null|User findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function isExistAnotherUserByApiToken(int $user_id, string $apiToken)
    {
        return !empty($this->createQueryBuilder('u')
            ->andWhere('u.id != :val')
            ->setParameter('val', $user_id)
            ->andWhere('u.apiToken = :val')
            ->setParameter('val', $apiToken)
            ->getQuery()
            ->getResult());
    }

    public function getUserQuery(int $userId)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :id')
            ->leftJoin('u.subscribe', 's')
            ->addSelect('s')
            ->setParameter('id', $userId)
            ->getQuery()
        ;
    }

    public function getUserByApiToken($apiToken): ?User
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.apiToken = :apiToken')
            ->setParameter('apiToken', $apiToken)
            ->getQuery();

        return (count($query->getResult()) > 0) ? $query->getSingleResult() : null;
    }
}
