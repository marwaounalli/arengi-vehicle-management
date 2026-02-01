<?php

namespace App\Repository;

use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\CarSearchCriteria;
use App\Model\AdminCarListCriteria;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    public function search(CarSearchCriteria $search): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($search->brand) {
            $brand = trim($search->brand);

            $qb->andWhere('LOWER(c.brand) LIKE :brand')
                ->setParameter('brand', '%' . mb_strtolower($brand) . '%');
        }
        if ($search->minPassengers !== null) {
            $qb->andWhere('c.passengers >= :minPassengers')
                ->setParameter('minPassengers', $search->minPassengers);
        }

        $map = [
            'createdAt' => 'c.createdAt',
            'brand' => 'c.brand',
            'model' => 'c.model',
            'passengers' => 'c.passengers',
        ];

        $sortField = $map[$search->sort] ?? 'c.createdAt';
        $dir = strtolower((string) $search->dir) === 'asc' ? 'ASC' : 'DESC';

        $qb->orderBy($sortField, $dir)
            ->addOrderBy('c.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function paginate(AdminCarListCriteria $criteria): array
    {
        $map = [
            'createdAt'  => 'c.createdAt',
            'brand'      => 'c.brand',
            'model'      => 'c.model',
            'passengers' => 'c.passengers',
        ];

        $sortField = $map[$criteria->sort];
        $direction = $criteria->dir === 'asc' ? 'ASC' : 'DESC';

        $offset = ($criteria->page - 1) * $criteria->limit;

        $qb = $this->createQueryBuilder('c')
            ->orderBy($sortField, $direction)
            ->addOrderBy('c.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($criteria->limit);

        $paginator = new Paginator($qb->getQuery(), true);

        $total = count($paginator);
        $pages = max(1, (int) ceil($total / $criteria->limit));

        return [
            'items' => iterator_to_array($paginator->getIterator(), false),
            'pagination' => [
                'page'  => $criteria->page,
                'pages' => $pages,
                'total' => $total,
                'limit' => $criteria->limit,
            ],
        ];
    }
}
