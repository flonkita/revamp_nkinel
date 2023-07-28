<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    /**
     * @Route("/cgv", name="cgv")
     */
    public function cgv(): Response
    {
        return $this->render('info/cgv.html.twig');
    }
    /**
     * @Route("/mentions_legales", name="mentions_legales")
     */
    public function mentions(): Response
    {
        return $this->render('info/mentions.html.twig');
    }
    /**
     * @Route("/conditions_utilisations", name="conditions_utilisations")
     */
    public function conditions(): Response
    {
        return $this->render('info/conditions_utilisations.html.twig');
    }
}
