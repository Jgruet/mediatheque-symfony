<?php

namespace App\Controller;

use App\Service\MemberAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/espace-adherent', name: 'member_area_')]
class MemberAreaController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(MemberAccess $access_provider): Response
    {
        $infos = $access_provider->checkAccess();

        return $this->render('front-office/member_area/index.html.twig', [
            'infos' => $infos,
        ]);
    }

    #[Route('/payer', name: 'payment')]
    public function payMembershipSubscription(MemberAccess $access_provider): Response
    {
        $access_provider->payMembershipSubscription();

        return $this->redirectToRoute('member_area_index');
    }
}
