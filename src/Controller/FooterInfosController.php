<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FooterInfosController extends AbstractController
{
    public function getInfos(Request $request): Response
    {
        $infos = [];
        $favLanguages = $request->getLanguages();
        $browser = $request->headers->get('User-Agent');

        $infos['ipVisitor'] = $request->getClientIp();
        $infos['favLang'] = $favLanguages[0];
        $infos['browser'] = $browser;

        return $this->render('public/partials/footer-infos.html.twig', [
            'infos' => $infos,
        ]);
    }
}
