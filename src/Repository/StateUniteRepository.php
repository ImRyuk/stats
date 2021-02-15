<?php

namespace App\Repository;

use App\Entity\StateUnite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StateUnite|null find($id, $lockMode = null, $lockVersion = null)
 * @method StateUnite|null findOneBy(array $criteria, array $orderBy = null)
 * @method StateUnite[]    findAll()
 * @method StateUnite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StateUniteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StateUnite::class);
    }

    // /**
    //  * @return StateUnite[] Returns an array of StateUnite objects
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
    public function findOneBySomeField($value): ?StateUnite
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
