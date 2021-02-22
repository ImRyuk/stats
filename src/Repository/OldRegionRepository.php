<?php

namespace App\Repository;

use App\Entity\OldRegion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OldRegion|null find($id, $lockMode = null, $lockVersion = null)
 * @method OldRegion|null findOneBy(array $criteria, array $orderBy = null)
 * @method OldRegion[]    findAll()
 * @method OldRegion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OldRegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OldRegion::class);
    }

    // /**
    //  * @return OldRegion[] Returns an array of OldRegion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OldRegion
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
