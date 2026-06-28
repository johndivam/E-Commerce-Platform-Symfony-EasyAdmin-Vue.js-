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

    public function getWithPagination(int $page, int $limit, ?string $search = null): Paginator
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.status = :status')
            ->setParameter('status', ProductStatus::AVAILABLE)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        if (!empty(trim($search))) {
            $qb->andWhere('r.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return new Paginator(
            $qb->getQuery()->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
            false
        );
    }

    public function findOneBySlugWithRelations(string $slug): ?Product
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->leftJoin('p.brand', 'b')
            ->addSelect('b')
            ->andWhere('p.slug = :slug')
            ->andWhere('p.status = :status')
            ->setParameter('slug', $slug)
            ->setParameter('status', ProductStatus::AVAILABLE)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
