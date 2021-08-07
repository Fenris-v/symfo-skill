<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getLatest(int $limit = 3)
    {
        $qb = $this->createQueryBuilder('c');
        return $qb->orderBy('c.createdAt', 'DESC')
            ->innerJoin('c.article', 'a')
            ->addSelect('a')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string|null $search
     * @param bool $withSoftDeletes
     * @return QueryBuilder
     */
    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        if ($search) {
            $qb->andWhere('c.content LIKE :search OR c.authorName LIKE :search OR a.title LIKE :search')
                ->setParameter('search', "%$search%");
        }

        if ($withSoftDeletes) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }

        return $qb->innerJoin('c.article', 'a')
            ->addSelect('a')
            ->orderBy('c.createdAt', 'DESC');
    }
}
