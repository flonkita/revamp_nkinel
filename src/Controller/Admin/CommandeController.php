<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Commande;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin", name="admin")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/commandes", name="_commandes")
     */
    public function index(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        $commandesQuery = $doctrine->getRepository(Commande::class)->findBy([
            'etat' => 'Validée',
        ]);

        $pagination = $paginator->paginate(
            $commandesQuery, // QueryBuilder
            $request->query->getInt('page', 1), // Numéro de page par défaut
            10 // Nombre d'éléments par page
        );

        return $this->render('admin/commandes/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * @Route("/commandes/{id}", name="_commande_detail", requirements={"id"="\d+"})
     */
    public function showCommande(ManagerRegistry $doctrine, int $id): Response
    {
        $commande = $doctrine->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('La commande avec l\'ID ' . $id . ' n\'existe pas.');
        }

        $user = $commande->getUser();

        return $this->render('admin/commandes/show.html.twig', [
            'commande' => $commande,
            'user' => $user
        ]);
    }

}
