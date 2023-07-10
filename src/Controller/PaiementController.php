<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    /**
     * @Route("/paiement", name="app_paiement")
     */
    public function index(PanierService $panierService): Response
    {
        // Stripe secret key
        $ssk = $this->getParameter('stripe.secretKey');
        Stripe::setApiKey($ssk); // On configure Stripe
        $tableauPourStripe = []; // Un tableau pour Stripe
    }
}
