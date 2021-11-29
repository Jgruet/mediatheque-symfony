<?php

namespace App\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class RedirectMaintenancePage implements EventSubscriberInterface
{

    private $maintenanceMode;
    private $twig;

    public function __construct(bool $maintenanceMode, Environment $twig)
    {
        $this->maintenanceMode = $maintenanceMode;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents()
    {
        return [
            // Lorsque l'événement se produit la méthode `onKernelRequest` est exécuté avec la priorité 10.
            // La priorité est importante puisque plusieurs classes peuvent souscrire au même événement.
            KernelEvents::REQUEST => ['onKernelRequest', 999]
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if ($this->maintenanceMode) {
            /* $response = new Response('Site en maintenance');
            $event->setResponse($response); */

            $response = new Response($this->twig->render('/maintenance/index.html.twig'), 503, ['Retry-After' => '3600']);
            $event->setResponse($response);
        }

        /* $response = $event->getResponse();
        // ... modify the response object
        // Creating cookies with arguments
        $cookie = new Cookie('custom-event-cookie', 'it works', 0);
        $response->headers->setCookie($cookie); */
    }
}
