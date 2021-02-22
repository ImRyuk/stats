<?php

namespace App\Repository;

use App\Entity\StatValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StatValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatValue[]    findAll()
 * @method StatValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatValue::class);
    }

    // /**
    //  * @return StatValue[] Returns an array of StatValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StatValue
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
