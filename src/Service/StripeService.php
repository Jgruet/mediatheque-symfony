<?php

namespace App\Service;

use App\Form\PenaltyPaymentType;
use App\Repository\PenaltyRepository;
use App\Service\MemberAccess;
use App\Service\PenaltyService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Stripe;
use stdClass;
use Stripe\Checkout\Session as StripeSession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class StripeService
{

    private $api_key;

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    public function setApiKey()
    {
        Stripe::setApiKey($this->api_key);
    }

    /**
     * @param null|array $params
     * @param null|array|string $options
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     *
     * @return static the created resource
     */
    public function createCheckoutSession($url, $amount)
    {
        return StripeSession::create([

            'line_items' => [[
                'price_data' => [
                    'currency' => 'EUR',
                    'unit_amount_decimal' => intVal($amount) * 100,
                    'product_data' => [
                        'name' => "Régulérisation pénalité médiathèque",
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $url . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $url . '?session_id={CHECKOUT_SESSION_ID}',
        ]);
    }


    public function retriveSession($id)
    {
        return StripeSession::retrieve($id);
    }
}
