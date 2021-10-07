<?php

namespace App\Repository;

use App\Entity\Cd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cd|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cd|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cd[]    findAll()
 * @method Cd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cd::class);
    }

    // /**
    //  * @return Cd[] Returns an array of Cd objects
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
    public function findOneBySomeField($value): ?Cd
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
