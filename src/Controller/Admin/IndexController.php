<?php

namespace App\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/admin", name="admin_")
 */
class IndexController extends AbstractController
{

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $isTopVideos = $videoRepository->findBy(["isTop" => true]);
        $articles = $articles->findAll();
        $team = $teamRepository->findAll();
        $aboutCategory = $aboutRepository->findAll(["category"]);

        return $this->render('admin/index.html.twig', [
            'isTopVideos' => $isTopVideos,
            'videos' => $videos,
            'team' => $team,
            'aboutCategory' => $aboutCategory
        ]);
    }
}
