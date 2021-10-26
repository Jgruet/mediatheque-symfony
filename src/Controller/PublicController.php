<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'public-')]

class PublicController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        return $this->render('front-office/index.html.twig', [
            'controller_name' => 'PublicController',
            'nom_de_la_variable' => 'toto',
        ]);
    }

    #[Route('/presentation', name: 'presentation')]
    public function presentation(): Response
    {
        return $this->render('front-office/presentation.html.twig', [
            'controller_name' => 'PublicController',
        ]);
    }
}
