<?php

namespace App\Service;

use App\Entity\Penalty;
use App\Entity\User;
use App\Repository\PenaltyRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Service qui transforme un malus en période de pénalité durant laquelle un membre ne peut pas emprunter de livre
 */
class PenaltyService
{

    private $penaltyRepository;
    private $em;

    public function __construct(PenaltyRepository $penaltyRepository, ManagerRegistry $em)
    {
        $this->penaltyRepository = $penaltyRepository;
        $this->em = $em->getManager();
    }

    public function calculatePenalty(int $malus, User $user)
    {


        $existingPenalty = $this->penaltyRepository->findOneBy(['user' => $user]);
        if ($existingPenalty != NULL) {
            $existingDate = $existingPenalty->getEndAt();
            $existingPenalty->setEndAt(new \DateTime(date("Y-m-d", strtotime($existingDate->format("Y-m-d") . " +" . $malus . " days"))));
            $this->em->flush();
        } else {
            $penalty = new Penalty();
            $penaltyEndAt = new \DateTime(date("Y-m-d", strtotime("now +" . $malus . " days")));
            $penalty->setUser($user);
            $penalty->setEndAt($penaltyEndAt);
            $this->em->persist($penalty);
            $this->em->flush();
        }
    }
}
