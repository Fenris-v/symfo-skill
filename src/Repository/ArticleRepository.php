<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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
