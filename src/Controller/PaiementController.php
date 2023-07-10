<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Article;
use App\Entity\Commande;
use Stripe\Checkout\Session;
use App\Service\PanierService;
use App\Entity\CommandeProduit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaiementController extends AbstractController
{
    /**
     * @Route("/paiement", name="app_paiement")
     */
    public function index(PanierService $panierService, EntityManagerInterface $em): Response
    {
        // Stripe secret key
        $ssk = $this->getParameter('stripe.secretKey');
        Stripe::setApiKey($ssk); // On configure Stripe
        $tableauPourStripe = []; // Un tableau pour Stripe

        // Créer la commande
        $commande = new Commande;
        $commande->setEtat('En attente de paiement');
        $commande->setToken(
            hash('sha256', random_bytes(32)) // Crée une chaîne de caractères aléatoire
        );

        $panier = $panierService->showCart();

        // On boucle pour remplir la commande
        // On boucle également pour remplir le formulaire Stripe
        foreach ($panier['article'] as $id => $ligne) {

            $quantite = $ligne['quantite'];
            /** @var Article */
            $article = $ligne['article'];

            // On remplit la commande
            $cp = new CommandeProduit;
            $cp->setQuantite($quantite);
            $cp->setArticle($article);

            $commande->addCommandeProduit($cp);


            // On remplit le tableau pour Stripe
            $tableauPourStripe[] = [
                'quantity' => $quantite,
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $article->getNom(),
                        'images' => [$article->getImage()] // Lien ABSOLU (qui commence par "http(s)://") ; Pas obligatoire
                    ],
                    'unit_amount' => $article->getPrix() * 100 // Prix en CENTIMES
                ]
            ];
        }

        // On sauvegarde la commande en BDD
        $em->persist($commande);
        $em->flush();

        $checkout = Session::create([
            'mode' => 'payment',
            'line_items' => $tableauPourStripe,
            'success_url' => $this->generateUrl('app_paiement_success', [
                'token' => $commande->getToken()
            ], UrlGeneratorInterface::ABSOLUTE_URL), // Lien ABSOLU (qui commence par "http(s)://")
            'cancel_url' => $this->generateUrl('app_paiement_fail', [
                'commande' => $commande->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL),  // Lien ABSOLU (qui commence par "http(s)://")
        ]);

        // On vide le panier
        $panierService->clear();

        return $this->redirect($checkout->url);
    }
}
