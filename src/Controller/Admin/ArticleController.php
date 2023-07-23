<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function new(Request $request, ManagerRegistry $doctrine, SessionInterface $session): Response
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
            $session->getFlashBag()->add('create_article', 'Un article a bien été ajouté.');
        return $this -> redirectToRoute('admin_article_index');

        }

        #Etape 3 : On envoie le formulaire dans la vue
        return $this->render('admin/article/new.html.twig', [
            'formArticle'=> $formArticle->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository, SessionInterface $session): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $formArticle = $this->createForm(ArticleType::class, $article);
        $formArticle->handleRequest($request);

        if ($formArticle->isSubmitted() && $formArticle->isValid()) {
            $data = $formArticle->getData();

            if ($formArticle->get('image')->getData() == null) {
                $image_name = $article->getImage();
            } else {
                $image_name = $formArticle->get('image')->getData()->getClientOriginalName();
                $image_name = uniqid() . $image_name;
                $formArticle->get('image')->getData()->move(
                    $this->getParameter('images_directory'),
                    $image_name
                );
            }


            if ($image_name) {
                $data->setImage($image_name);
            }

            $entityManager->persist($data);
            $entityManager->flush();
            $articleRepository->add($article, true);

            $session->getFlashBag()->add('edit_article', 'Un article a bien été modifié.');
            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/edit.html.twig', [
            'article' => $article,
            'formArticle' => $formArticle,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function delete($id, Article $article, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
         #Etape 1 : On appelle l'entity manager de doctrine
         $entityManager = $doctrine->getManager();

         #Etape 2 : On récupère (grâce au repository de doctrine) l'objet que l'on souhaite modifier
         $article = $doctrine->getRepository(Article::class)->find($id);

         #Etape 3 : On supprime à l'aide de l'entity manager 
         $entityManager->remove($article);

         #Etape 4 : On valide les modifications
         $entityManager->flush();

         $session->getFlashBag()->add('delete_article', 'Un article a bien été supprimé.');

        return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
