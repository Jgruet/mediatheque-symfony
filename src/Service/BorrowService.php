<?php

namespace App\Service;

use App\Entity\Borrow;
use App\Entity\Document;
use App\Entity\User;
use App\Repository\DocumentRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Service qui créé des emprunts
 */
class BorrowService
{


    private $start_date;
    private $em;
    private $end_date;
    private $documentRepository;

    public function __construct($borrowTime, ManagerRegistry $doctrine, DocumentRepository $documentRepository)
    {
        $this->start_date = new \DateTime();
        $this->end_date = new \DateTime(date("Y-m-d", strtotime("now +" . $borrowTime . " days")));

        $this->em = $doctrine->getManager();

        $this->documentRepository = $documentRepository;
    }

    //public function
    /**
     * Create and persist in db a borrow
     *
     * @return void
     */
    public function createBorrow(User $user, Document $document,)
    {
        $borrow = new Borrow();
        $borrow->setUser($user);
        $borrow->setDocument($document);
        $borrow->setStartAt($this->start_date);
        $borrow->setEndAt($this->end_date);
        $borrow->setActive(true);

        $this->em->persist($borrow);
        $this->em->flush();
    }



    public function endBorrow(Borrow $borrow, int $conservation): int
    {
        $malus = 0;

        if ($borrow->getEndAt() < new \DateTime()) {
            // calcule les jours de retard
            $daysLate = $borrow->getEndAt()->diff(new \DateTime())->days;
            $borrow->setDayLate($daysLate);
            $malus += intVal($daysLate);
        } else {
            $borrow->setDayLate(0);
        }


        // rentre la vraie date de fin
        $borrow->setEndAt(new \DateTime());

        // set active à 0
        $borrow->setActive(false);


        // Gestion de l'usure
        $doc = $this->documentRepository->find($borrow->getDocument()->getId());
        // Si le document était en meilleur état quand il a été prété
        if ($doc->getConservation() > $conservation) {
            $gap = $doc->getConservation() - $conservation;

            switch ($gap) {
                case 1:
                    $malus += 3;
                    break;
                case 2:
                    $malus += 6;
                    break;
                case 3:
                    $malus += 9;
                    break;
                case 4:
                    $malus += 12;
                    break;
            }
        }

        $doc->setConservation($conservation);

        $this->em->flush();
        return $malus;
    }
}
