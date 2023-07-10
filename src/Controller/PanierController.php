<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="app_panier")
     */
    public function index(PanierService $panierService): Response
    {

        return $this->render('panier/index.html.twig', [
            "items" => $panierService->showCart(),
            "total" => $panierService->totalcart(),
        ]);

    }

    /**
     * @Route("/panier/add/{id}", name="panier_add")
     */
    public function add($id, PanierService $panierService)
    {
      $panierService->add($id);

      $this->addFlash('add_panier', "Le produit a bien été ajouté a votre panier");
        return $this->redirectToRoute('app_panier');
    }

    /**
     * @Route("/panier/delete/{id}", name="panier_delete")
     */
    public function delete($id, PanierService $panierService)
    {
        #On récupere la fonction remove dans la la variable PanierService
        $panierService->remove($id);

        #On envoie une notification informative qu'un produit a bien été supprimé
        $this->addFlash('remove_panier', "Un produit a bien été retiré de votre panier");
        #On redirige vers le panier
        return $this->redirectToRoute('app_panier');
    }

    /**
     * @Route("/panier/clear", name="panier_clear")
     */
    public function clearPanier(PanierService $panierService)
    {
        #On vide le panier
        $panierService->clear();

        #On envoie une notification informative que tous les produits ont été retirés
        $this->addFlash('clear_panier', "Tous les produits ont été retirés de votre panier");
        #On redirige vers le panier
        return $this->redirectToRoute('app_panier');
    }   

}
