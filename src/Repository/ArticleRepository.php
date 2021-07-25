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

     /**
      * @return Article[] Returns an array of Article objects
      */
    public function findLatestPublished(): array
    {
        return $this->published($this->latest())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param QueryBuilder|null $builder
     * @return QueryBuilder
     */
    private function published(?QueryBuilder $builder = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($builder)
            ->andWhere('art.publishedAt IS NOT NULL');
    }

    /**
     * @param QueryBuilder|null $builder
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $builder): QueryBuilder
    {
        return $builder ?? $this->createQueryBuilder('art');
    }

    /**
     * @param QueryBuilder|null $builder
     * @return QueryBuilder
     */
    private function latest(?QueryBuilder $builder = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($builder)
            ->orderBy('art.publishedAt', 'DESC');
    }
}