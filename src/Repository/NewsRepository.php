<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    /**
     * @return News[]
     */
    public function findRecent(?int $limit = null, ?int $offset = null): array
    {
        return $this->findBy([], ['updated_at' => 'DESC'], $limit, $offset);
    }

    /**
     * @return News[]
     */
    public function findByQuery(string $query, ?int $limit = null, ?int $offset = null): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.title LIKE ?1')
            ->orderBy('n.updated_at', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->setParameter(1, '%'.\addcslashes($query, '%_\\').'%', ParameterType::STRING)
            ->execute();
    }
}
