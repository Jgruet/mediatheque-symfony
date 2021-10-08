<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Renvoie les livres qui ont un nb mini de page passé en param
     * Exemple d'utilisation de DQL avec param
     * premier exemple avec DQL, ensuite en queryBuilder
     * @return Book[]
     */
    public function findBigBooks(int $nb_page)
    {
        /* $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT b
            FROM App\Entity\Book b
            WHERE b.nb_page > :nb_page
            ORDER BY b.nb_page ASC'
        )->setParameter('nb_page', $nb_page);

        // returns an array of Book objects
        return $query->getResult(); */

        $query = $this
            ->createQueryBuilder('b')
            ->select('b')
            ->where("b.nb_page > :nb_page")
            ->orderBy(' b.nb_page', 'ASC')
            ->setParameter('nb_page', $nb_page)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Renvoie la liste des livres dont l'id est compris entre un min et max passé en param
     * Exemple d'utilisation de between
     * @return Book[]
     */
    public function findBooksTitleByRangeId(int $start, int $stop): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT b.title
            FROM App\Entity\Book b
            WHERE b.id BETWEEN ?1 AND ?2'
        )->setParameter(1, $start)->setParameter(2, $stop);

        // returns an array of Book objects
        return $query->getResult();
    }




    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
