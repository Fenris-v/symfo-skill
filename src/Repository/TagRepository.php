<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @param string|null $search
     * @param bool $withSoftDeletes
     * @return QueryBuilder
     */
    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        if ($search) {
            $qb->andWhere('t.name LIKE :search OR a.title LIKE :search')
                ->setParameter('search', "%$search%");
        }

        if ($withSoftDeletes) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }

        return $qb->orderBy('t.createdAt', 'DESC')
            ->leftJoin('t.articles', 'a')
            ->addSelect('a');
    }
}
