<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/article", name="admin_article_")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        #Etape 1 : Créer un objet vide
        $article = new Article;

        #Etape 2 : Créer le formulaire
        $formArticle = $this->createForm(ArticleType::class, $article);

        $formArticle->handleRequest($request);
        if($formArticle->isSubmitted() && $formArticle->isValid())
        {
            $data = $formArticle->getData();

            if ($formArticle->get('image')->getData() == null) {
                $image = null;
            } else {
                $image = $formArticle->get('image')->getData()->getClientOriginalName();
            }
            if ($image) {
                $image = $formArticle->get('image')->getData()->getClientOriginalName();
                $data->setImage($image);
                $formArticle->get('image')->getData()->move(
                    $this->getParameter('images_directory'),
                    $image
                );

            }
            #Etape 1 : On appel l'entity manager de doctrine
            $entityManager = $doctrine->getManager();

            #Etape 3 : on indique a doctrine que l'on souhaite préparer l'enregistrement d'un nouvel élément
            $entityManager->persist($data);

            #etape 4: on valide a doctrine que l'on veut enregisterer/persister en BDD
            $entityManager->flush();
            #etape 5: on affiche ou on redirge vers une autre page 
            $this->addFlash('create','Un article a fait son apparition !');
        return $this -> redirectToRoute('admin_article_index');

        }

        #Etape 3 : On envoie le formulaire dans la vue
        return $this->render('admin/article/new.html.twig', [
            'formArticle'=> $formArticle->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('admin/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $formArticle = $this->createForm(ArticleType::class, $article);
        $formArticle->handleRequest($request);

        if ($formArticle->isSubmitted() && $formArticle->isValid()) {
            $articleRepository->add($article, true);

            return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/edit.html.twig', [
            'article' => $article,
            'formArticle' => $formArticle,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
