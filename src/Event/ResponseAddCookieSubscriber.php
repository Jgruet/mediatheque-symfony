<?php

namespace App\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Cookie;

class ResponseAddCookieSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents()
    {
        return [
            // Lorsque l'événement se produit la méthode `onKernelResponse` est exécuté avec la priorité 10.
            // La priorité est importante puisque plusieurs classes peuvent souscrire au même événement.
            KernelEvents::RESPONSE => ['onKernelResponse', 10]
        ];
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        // ... modify the response object
        // Creating cookies with arguments
        $cookie = new Cookie('custom-event-cookie', 'it works', 0);
        $response->headers->setCookie($cookie);
    }
}
