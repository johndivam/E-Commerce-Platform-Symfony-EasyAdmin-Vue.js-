<?php

namespace App\Repository;

use App\Entity\Product;
use App\Enum\ProductStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getWithPagination(int $page, int $limit): Paginator
    {
        return new Paginator(
            $this->createQueryBuilder('r')
            ->andWhere('r.status = :status')
            ->setParameter('status', ProductStatus::AVAILABLE)
            ->setFirstResult(($page -1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
            false
        );
    }
}
