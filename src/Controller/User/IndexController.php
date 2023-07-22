<?php

namespace App\Controller\User;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="user_")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        $nombreCommandes = $commandeRepository->countValidatedCommandesByUser($user);

        return $this->render('user/index.html.twig', [
            'nombreCommandes' => $nombreCommandes,
            'user' => $user,
        ]);
    }
}
