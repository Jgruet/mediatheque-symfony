<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Si possible, utiliser une interface au lieu d'une classe
// Dans ce cas, les 2 classes implémentent la même interface donc, on fait appel à ces dernières
use App\Service\DateService;
use App\Service\DateServiceCustomizable;

class FooterInfosController extends AbstractController
{
    public function getInfos(Request $request, DateService $date, DateServiceCustomizable $dateCustom): Response
    {
        // moyen d'injecter un service directement dans le code - le service doit être publique - ancienne version ?
        //$date = $this->container->get(DateService::class);

        $infos = [];
        $favLanguages = $request->getLanguages();
        $browser = $request->headers->get('User-Agent');

        $infos['ipVisitor'] = $request->getClientIp();
        $infos['favLang'] = $favLanguages[0];
        $infos['browser'] = $browser;

        $currentDate = $date->getCurrentDay();
        $daySinceNewYearsDay = $date->daysSinceNewYearsDay();

        $currentDateCustom = $dateCustom->getCurrentDay();
        $daySinceNewYearsDayCustom = $dateCustom->daysSinceNewYearsDay();


        return $this->render('front-office/partials/footer-infos.html.twig', [
            'infos' => $infos,
            'current_date' => $currentDate,
            'day_since_new_years_day' => $daySinceNewYearsDay,
            'current_date_Custom' => $currentDateCustom,
            'day_since_new_years_day_Custom' => $daySinceNewYearsDayCustom,
        ]);
    }
}
