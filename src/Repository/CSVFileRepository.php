<?php

namespace App\Repository;

use App\Entity\CSVFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CSVFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method CSVFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method CSVFile[]    findAll()
 * @method CSVFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CSVFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CSVFile::class);
    }

    // /**
    //  * @return CSVFile[] Returns an array of CSVFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CSVFile
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
