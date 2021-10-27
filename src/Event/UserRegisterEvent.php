<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Entity\User;

class UserRegisterEvent extends Event
{
    /*
    * Le nom de l'événement.
    * Par convention, les noms sont constitués de 2 ou 3 segments contenant le nom d'une entité et l'action déclencheuse
    */
    public const NAME = 'user.register';

    /*
    * @var $target L'objet cible lié à l'événement
    */
    private $target;

    /**
     * Constructeur
     * Un objet, qui est la cible de l'événement est lié à au nouvel objet lors du déclenchement
     *
     * @param $user Utilisateur qui vient de créer un compte
     */
    public function __construct(User $user)
    {
        $this->target = $user;
    }

    /**
     * Un accesseur qui délivre juste l'objet cible à un écouteur qui en a besoin
     * @return {[type]} [description]
     */
    public function getTarget(): User
    {
        return $this->target;
    }
}
