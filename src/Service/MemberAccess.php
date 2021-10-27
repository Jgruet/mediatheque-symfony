<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Security;

class MemberAccess
{
    private $user;
    private $role_user;
    private $base_fees;
    private $em;

    public function __construct(Security $security, int $memberFees, EntityManager $em)
    {
        $this->user = $security->getUser();
        $this->role_user = $security->getUser()->getRoles();
        $this->base_fees = $memberFees;
        $this->em = $em;
    }

    public function checkAccess()
    {
        $infos = [];
        $infos['access'] = false;
        $infos['fees'] = $this->amountToPay();

        if (!in_array('ROLE_MEMBER', $this->role_user)) {
            return $infos;
        } else {
            $infos['access'] = true;
            return $infos;
        }
    }

    public function amountToPay()
    {
        $fees = $this->base_fees;
        $age = $this->user->getAge();
        $salary = $this->user->getAnnualSalary();
        $nbChildren = $this->user->getNbChildren();
        $cp = $this->user->getPostalCode();

        $fees = $this->ageReduction($fees, $age);
        $fees = $this->salaryReduction($fees, $salary, $nbChildren);
        $fees = $this->residentReduction($fees, $cp);

        return number_format($fees, 0);
    }

    public function ageReduction($price, $age)
    {
        if ($age < 10) {
            $price = 0;
            return $price;
        } else if ($age < 18) {
            $price = $price / 2;
            return $price;
        }
        return $price;
    }
    public function salaryReduction($price, $salary, $nbChildren)
    {
        $qf = ($salary / 12) / (1 + ($nbChildren / 2));
        $price = $price * ($qf / 1000);
        return $price;
    }

    public function residentReduction($price, $cp)
    {
        if ($cp == '33700') {
            $price = $price * 0.8;
            return $price;
        } else {
            return $price;
        }
    }

    public function payMembershipSubscription()
    {
        $this->user->setRoles(['ROLE_MEMBER']);
        $this->em->flush();
    }
}

// Récupérer le role de l'utilisateur
// Si le role est user et non membre
// Calculer les frais d'adhésion en rapport avec ses infos présentent en db
// Afficher les frais et un bouton payer
// Quand il clique sur payer, le passer en membre