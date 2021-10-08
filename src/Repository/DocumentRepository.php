<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
     * Renvoi les oeuvres dont le nom de famille de l'auteur commence par la lettre passée en param
     * exemple d'utilisation de LIKE
     * @return Document[]
     */
    public function findDocumentsByAuthorFirstLetter(string $start): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT d.title, a.lastName
            FROM App\Entity\Document d JOIN d.author a
            WHERE a.lastName LIKE :start "
        )->setParameter('start', $start . '%');

        // returns an array of Book objects
        return $query->getResult();
    }

    /**
     * Renvoi les oeuvres dont le nom de famille de l'auteur commence par la lettre passée en param ainsi que d'une catégorie (param aussi)
     * la jointure est utile uniquement parce qu'on a les param, on se contente sinon de choper Document et sur le template on affiche auteur et [category]
     * @return Document[]
     */
    public function findDocumentsByAuthorFirstLetterAndCat(string $start, string $cat): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            "SELECT d
            FROM App\Entity\Document d
            JOIN d.author a
            JOIN d.category c
            WHERE a.lastName LIKE :start AND c.name = :category "
        )->setParameter('start', $start . '%')->setParameter('category', $cat);

        // returns an array of Book objects
        return $query->getResult();
    }

    /**
     * Recupere les cd dont la durée est supérieur à la durée moyenne des cd, exemple de rq imbriquée
     * Premier exemple avec DQL, deuxieme avec queryBuilder (obligé d'utiliser from car on veut des cd alors qu'on est dans document)
     * @return Document[]
     */
    public function findCdByDuration(): array
    {
        /* $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT cd
            FROM App\Entity\Cd cd
            WHERE cd.duration > (SELECT AVG(cd2.duration) FROM App\Entity\Cd cd2)"
        );

        // returns an array of Book objects
        return $query->getResult(); */

        $secondQb = $this->createQueryBuilder('a');
        $subQuery = $secondQb->select('AVG(c.duration)')->from('App\Entity\Cd', 'c')->getDQL();

        $qb = $this->createQueryBuilder('b');
        return $qb->select('cd')
            ->from('App\Entity\Cd', 'cd')
            ->where("cd.duration > ($subQuery)")
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return Document[] Returns an array of Document objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Document
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
