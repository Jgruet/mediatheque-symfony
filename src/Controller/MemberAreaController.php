<?php

namespace App\Controller;

use App\Form\PaymentType;
use App\Service\MemberAccess;
use App\Service\PenaltyService;
use App\Service\StripeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/espace-adherent', name: 'member_area_')]

class MemberAreaController extends AbstractController
{

    #[Route('/', name: 'index')]
    /**
     *@IsGranted("ROLE_USER")
     */
    public function index(MemberAccess $access_provider, Security $security, PenaltyService $penalty, Request $request, StripeService $stripeService): Response
    {

        $user = $security->getUser();
        $infos = $access_provider->checkAccess();
        $penaltyFee = 0;


        // Traitement redirection après paiement STRIPE
        $stripeService->setApiKey();
        if ($request->get('session_id') != NULL) {
            $infosession = $stripeService->retriveSession($request->get('session_id'));

            // Si l'utilisateur revient de Stripe en ayant une pénalité à enlever, c'est qu'il vient de payer une amende
            if ($penalty->checkPenalty($user) != NULL) {
                if (isset($infosession) && $infosession->payment_status == "paid") {
                    $penalty->removePenalty($user);
                    $this->addFlash(
                        'paymentStatus',
                        'Paiement enregistré, pénalité supprimée'
                    );
                } else {
                    $this->addFlash(
                        'paymentStatus',
                        'Paiement non enregistré. Veuillez réessayer'
                    );
                }
            }

            // Si l'utilisateur qui revient de Stripe sans être membre, c'est qu'il vient de payer une adhésion
            if ($infos['access'] == false) {
                if (isset($infosession) && $infosession->payment_status == "paid") {
                    $access_provider->payMembershipSubscription();
                    $infos['access'] = true;
                    $this->addFlash(
                        'paymentStatus',
                        'Paiement enregistré, adhésion activée'
                    );
                } else {
                    $this->addFlash(
                        'paymentStatus',
                        'Paiement non enregistré. Veuillez réessayer'
                    );
                }
            }
        }

        // Si on est membre
        if ($infos['access'] == true) {
            $penaltyFee = $penalty->checkPenalty($user);

            if ($penaltyFee != NULL) {
                $ObjBorrow = new stdClass(); // objet php natif le plus basique, customizable - utilisé ici pour stocker l'id et le passer au formType
                $ObjBorrow->amount = $penaltyFee;

                $form = $this->createForm(PaymentType::class, $ObjBorrow);
                $formAction = 'member_area_payment-penaltyFee';
            }
        }

        // Si on est pas membre
        if ($infos['access'] == false) {
            $membershipFees = $infos['fees'];

            $ObjBorrow = new stdClass(); // objet php natif le plus basique, customizable - utilisé ici pour stocker l'id et le passer au formType
            $ObjBorrow->amount = $membershipFees;

            $form = $this->createForm(PaymentType::class, $ObjBorrow);
            $formAction = 'member_area_payment-membershipFee';
        }

        return $this->renderForm('front-office/member_area/index.html.twig', [
            'infos' => $infos,
            'user' => $user,
            'penaltyFee' => $penaltyFee,
            'form' => isset($form) ? $form : NULL,
            'formAction' => isset($form) ? $formAction : NULL,
        ]);
    }


    #[Route('/payer-amende', name: 'payment-penaltyFee', methods: ['POST'])]
    public function stripeCreateSession(Request $request, StripeService $stripeService)
    {
        $url = $_SERVER['HTTP_ORIGIN'] . $this->generateUrl('member_area_index');

        $ObjBorrow = new stdClass(); // objet php natif le plus basique, customizable - utilisé ici pour stocker l'id et le passer au formType
        $ObjBorrow->amount = NULL;

        $form = $this->createForm(PaymentType::class, $ObjBorrow);
        $form->handleRequest($request); //hydrate mon objet avec les données qui viennent du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $stripeService->setApiKey();
            $checkout_session = $stripeService->createCheckoutSession($url, $ObjBorrow->amount);
            return $this->redirect($checkout_session->url);
        }
    }


    #[Route('/payer-adhesion', name: 'payment-membershipFee', methods: ['POST'])]
    public function memberShipPayment(Request $request, StripeService $stripeService)
    {
        $url = $_SERVER['HTTP_ORIGIN'] . $this->generateUrl('member_area_index');

        $ObjBorrow = new stdClass(); // objet php natif le plus basique, customizable - utilisé ici pour stocker l'id et le passer au formType
        $ObjBorrow->amount = NULL;

        $form = $this->createForm(PaymentType::class, $ObjBorrow);
        $form->handleRequest($request); //hydrate mon objet avec les données qui viennent du formulaire

        if ($form->isSubmitted() && $form->isValid()) {

            $stripeService->setApiKey();
            $checkout_session = $stripeService->createCheckoutSession($url, $ObjBorrow->amount);
            return $this->redirect($checkout_session->url);
        }
    }
}
