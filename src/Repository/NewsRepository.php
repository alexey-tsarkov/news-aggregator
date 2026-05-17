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
        $qb = $this->createQueryBuilder('n')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $query = \trim(\preg_replace('/\\P{L}+/mu', $query, ' '));
        if ($query !== '') {
            $qb->where('MATCH (n.title, n.summary, n.published_by) AGAINST (?1 WITH QUERY EXPANSION)')
                ->setParameter(1, $query, ParameterType::STRING);
        } else {
            $qb->orderBy('n.updated_at', 'DESC');
        }

        return $qb->getQuery()->execute();
    }
}
