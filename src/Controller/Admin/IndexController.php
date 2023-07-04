<?php

namespace App\Controller\Admin;

use App\Repository\ArticleRepository;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ArticleRepository $articleRepository, UserRepository $userRepository, CommandeRepository $commandeRepository): Response
    {
        // $isTopVideos = $videoRepository->findBy(["isTop" => true]);
        $articles = $articleRepository->findAll();
        $users = $userRepository->findAll();
        $commande = $commandeRepository->findAll();

        return $this->render('admin/index.html.twig', [
            // 'isTopVideos' => $isTopVideos,
            'articles' => $articles,
            'users' => $users,
            'commande' => $commande
        ]);
    }
}
