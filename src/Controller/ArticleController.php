<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    /**
     * @Route("/article/{id}", name="article_detail", requirements={"id":"\d+"})
     */
    public function detail($id, ManagerRegistry $doctrine)
    {
        #Etape 1 : Récupérer un livre 
        $article = $doctrine->getRepository(Article::class)->find($id);

        return $this->render('article/index.html.twig', [
            "article" => $article
        ]);
    }
}
