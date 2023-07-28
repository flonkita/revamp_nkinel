<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/user", name="user_")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/commandes", name="commandes")
     */
    public function commandes(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer les commandes de l'utilisateur depuis la base de données
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findBy([
            'user' => $user,
        ]);

        // Passer les commandes au template pour les afficher dans le tableau
        return $this->render('user/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}
