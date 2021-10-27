<?php

namespace App\Event;

use App\Event\UserRegisterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

class UserRegisterSubscriber implements EventSubscriberInterface
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            // Lorsque l'événement se produit la méthode `onBorrowingDocument` est exécuté avec la priorité 10.
            // La priorité est importante puisque plusieurs classes peuvent souscrire au même événement.
            UserRegisterEvent::NAME => ['onUserRegister', 10]
        ];
    }

    public function onUserRegister(UserRegisterEvent $event)
    {
        // code qui s'exécute quand l'evenement est lancé
        //$date = new \DateTime();
        $this->logger->info("User " . $event->getTarget()->getEmail() . " just register");
    }
}
