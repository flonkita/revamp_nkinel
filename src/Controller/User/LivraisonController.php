<?php

namespace App\Controller\User;

use App\Entity\Livraison;
use App\Form\LivraisonType;
use App\Repository\LivraisonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/livraison")
 */
class LivraisonController extends AbstractController
{
    /**
     * @Route("/", name="app_livraison_index", methods={"GET"})
     */
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('user/livraison/index.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_livraison_new", methods={"GET", "POST"})
     */
    public function new(Request $request, LivraisonRepository $livraisonRepository): Response
    {
        $livraison = new Livraison();
        $livraison->setUsers($this->getUser());
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraisonRepository->add($livraison, true);

            return $this->redirectToRoute('app_paiement', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/livraison/new.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_livraison_show", methods={"GET"})
     */
    public function show(Livraison $livraison): Response
    {
        return $this->render('user/livraison/show.html.twig', [
            'livraison' => $livraison,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_livraison_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Livraison $livraison, LivraisonRepository $livraisonRepository): Response
    {
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraisonRepository->add($livraison, true);

            return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/livraison/edit.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_livraison_delete", methods={"POST"})
     */
    public function delete(Request $request, Livraison $livraison, LivraisonRepository $livraisonRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livraison->getId(), $request->request->get('_token'))) {
            $livraisonRepository->remove($livraison, true);
        }

        return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
    }
}
