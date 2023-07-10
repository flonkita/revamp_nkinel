<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Article;
use App\Entity\Commande;
use Stripe\Checkout\Session;
use App\Service\PanierService;
use App\Entity\CommandeProduit;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaiementController extends AbstractController
{
    /**
     * @Route("/paiement", name="app_paiement")
     */
    public function index(PanierService $panierService, EntityManagerInterface $em, SessionInterface $session): Response
    {
        // Stripe secret key
        $ssk = $this->getParameter('stripe.secretKey');
        Stripe::setApiKey($ssk); // On configure Stripe
        $tableauPourStripe = []; // Un tableau pour Stripe

        // Créer la commande
        $commande = new Commande;
        $commande->setDate(new \DateTimeImmutable());
        $commande->setTotal($panierService->total());
        $commande->setEtat('En attente de paiement');
        $commande->setToken(
            hash('sha256', random_bytes(32)) // Crée une chaîne de caractères aléatoire
        );

        $panier = $panierService->showCart();


        // On boucle pour remplir la commande
        // On boucle également pour remplir le formulaire Stripe
        foreach ($panier as $id => $ligne) {

            $quantity = $ligne['quantity'];
            /** @var Article */
            $article = $ligne['article'];

            // On remplit la commande
            $cp = new CommandeProduit;
            $cp->setQuantite($quantity);
            $cp->setArticle($article);

            $commande->addCommandeProduit($cp);


            // On remplit le tableau pour Stripe
            $tableauPourStripe[] = [
                'quantity' => $quantity,
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
        $em->flush(); // Lien ABSOLU (qui commence par "http(s)://")
    
        $session = Session::create([
            'mode' => 'payment',
            'line_items' => $tableauPourStripe,
            'success_url' => $this->generateUrl('app_paiement_success', [
                'token' => urlencode($commande->getToken()),
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_paiement_fail', [
                'commande' => urlencode($commande->getId())
            ], UrlGeneratorInterface::ABSOLUTE_URL),  // Lien ABSOLU (qui commence par "http(s)://")
        ]);

        // On vide le panier
        $panierService->clear();

        return $this->redirect($session->url);
    }

    /**
     * Le paiement a réussi
     * On "valide" la commande
     */
    /**
     * @Route("/paiement/success/{token}", name="app_paiement_success")
     */
    public function apres(string $token, CommandeRepository $commandeRepository, EntityManagerInterface $em): Response
    {
        $commande = $commandeRepository->findOneBy(['token' => $token]);
        $commande->setEtat('Validée');
        $em->persist($commande);
        $em->flush();

        return $this->render('payment/success.html.twig');
    }

    /**
     * Le paiement a échoué
     * On supprime la commande
     * On recrée le panier
     */
    /**
     * @Route("/paiement/echec/{commande}", name="app_paiement_fail")
     */
    public function retournerAAvant(Commande $commande, PanierService $panierService, EntityManagerInterface $em): Response
    {
        $panierService->remplirPanier($commande);
        $em->remove($commande);
        $em->flush();

        return $this->render('payment/cancel.html.twig');
    }
}
