<?php

namespace App\Event;

use App\Event\DocumentPrintEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DocumentPrintSubscriber implements EventSubscriberInterface
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            // Lorsque l'événement se produit la méthode `onBorrowingDocument` est exécuté avec la priorité 10.
            // La priorité est importante puisque plusieurs classes peuvent souscrire au même événement.
            DocumentPrintEvent::NAME => ['onDocumentPrint', 10]
        ];
    }

    public function onDocumentPrint(DocumentPrintEvent $event)
    {
        // code qui s'exécute quand l'evenement est lancé
        $nbViews = $event->getTarget()->getNbViews();
        $nbViews++;
        $event->getTarget()->setNbViews($nbViews);

        $this->em->flush();
    }
}
