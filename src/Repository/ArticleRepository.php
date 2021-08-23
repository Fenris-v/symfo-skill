<?php

namespace App\Repository;

use App\Entity\Article;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function getBySlug($slug)
    {
        return $this->getOrCreateQueryBuilder()
            ->andWhere('a.slug=:slug')
            ->setParameter('slug', $slug)
            ->innerJoin('a.comments', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult()[0];
    }

    /**
     * @param string|null $search
     * @param bool $withSoftDeletes
     * @return QueryBuilder
     */
    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');

        if ($search) {
            $qb->andWhere('a.title LIKE :search OR a.description LIKE :search OR a.body LIKE :search OR u.firstName LIKE :search')
                ->setParameter('search', "%$search%");
        }

        if ($withSoftDeletes) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }

        $qb->innerJoin('a.author', 'u')->addSelect('u');
        return $this->latest($qb);
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findLatestPublished(): array
    {
        return $this->published($this->latest())
            ->leftJoin('a.comments', 'c')
            ->addSelect('c')
            ->leftJoin('a.tags', 't')
            ->addSelect('t')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    public function latest(?QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->orderBy('a.publishedAt', 'DESC');
    }

    /**
     * @return mixed
     */
    public function findAllPublishedLastWeek(): mixed
    {
        return $this->published($this->latest())
            ->andWhere('a.publishedAt >= :week_ago')
            ->setParameter('week_ago', new DateTime('-7 days'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @return int
     * @throws Exception
     */
    public function countPublishedByDate(string $dateFrom, string $dateTo): int
    {
        return $this->published($this->latest())
            ->andWhere('a.publishedAt >= :from')
            ->andWhere('a.publishedAt <= :to')
            ->setParameter('from', new DateTime($dateFrom))
            ->setParameter('to', new DateTime($dateTo))
            ->select('COUNT(a.id) AS count')
            ->getQuery()
            ->getSingleResult()['count'];
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @return int
     * @throws Exception
     */
    public function countCreatedByDate(string $dateFrom, string $dateTo): int
    {
        return $this->latest()
            ->andWhere('a.createdAt >= :from')
            ->andWhere('a.createdAt <= :to')
            ->setParameter('from', new DateTime($dateFrom))
            ->setParameter('to', new DateTime($dateTo))
            ->select('COUNT(a.id) AS count')
            ->getQuery()
            ->getSingleResult()['count'];
    }

    /**
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function published(?QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('a.publishedAt IS NOT NULL');
    }

    /**
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('a');
    }
}
