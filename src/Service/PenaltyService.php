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
            if ($existingDate < new \DateTime()) {
                $penaltyEndAt = new \DateTime(date("Y-m-d", strtotime("now +" . $malus . " days")));
                $existingPenalty->setEndAt($penaltyEndAt);
                $this->em->flush();
            } else {
                $existingPenalty->setEndAt(new \DateTime(date("Y-m-d", strtotime($existingDate->format("Y-m-d") . " +" . $malus . " days"))));
                $this->em->flush();
            }
        } else {
            $penalty = new Penalty();
            $penaltyEndAt = new \DateTime(date("Y-m-d", strtotime("now +" . $malus . " days")));
            $penalty->setUser($user);
            $penalty->setEndAt($penaltyEndAt);
            $this->em->persist($penalty);
            $this->em->flush();
        }
    }

    /**
     * Return null if user has no penalty or calculate penalty fee
     *
     * @param User $user
     * @return null|int
     */
    public function checkPenalty(User $user): null|int
    {
        $penalty = $this->penaltyRepository->findOneBy(['user' => $user]);

        // Si j'ai une pénalité et qu'elle est en cours
        if ($penalty != NULL && $penalty->getEndAt() > new \DateTime()) {
            return $this->calculateFee($penalty);
        } else {
            return null;
        }
    }

    /**
     * Transform days of penalty remaining into int used as penalty fee
     *
     * @param Penalty $penalty
     * @return int
     */
    public function calculateFee(Penalty $penalty): int
    {
        $daysRemaining = intVal($penalty->getEndAt()->diff(new \DateTime())->days);
        $amountToPay = intVal($daysRemaining * 1.2);
        return $amountToPay;
    }

    public function removePenalty(User $user)
    {
        $penalty = $this->penaltyRepository->findOneBy(['user' => $user]);
        $this->em->remove($penalty);
        $this->em->flush();
    }
}
