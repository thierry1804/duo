<?php

namespace App\Repository;

use App\Entity\WishlistLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WishlistLine>
 *
 * @method WishlistLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method WishlistLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method WishlistLine[]    findAll()
 * @method WishlistLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishlistLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WishlistLine::class);
    }

//    /**
//     * @return WishlistLine[] Returns an array of WishlistLine objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WishlistLine
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
