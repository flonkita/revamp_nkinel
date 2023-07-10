<?php

namespace App\Service;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService {

    protected $session;
    protected $articleRepository;

    public function __construct(SessionInterface $session, ArticleRepository $articleRepository)
    {
        $this->session= $session;
        $this->articleRepository= $articleRepository;
    }


    public function showCart(): array {
        #On récupere la session 'panier' si elle existe - sinon elle est créée avec un tableau vide
        $panier = $this->session->get('panier', []);

        #Variable tableau
        $panierData = [];
        
        #On boucle sur la session 'panier' pour récuperer proprement l'objet (au lieu de l'id) et la quantité
        foreach($panier as $id => $quantity)
        {
            $panierData[] = [
                "article" => $this->articleRepository->find($id),
                "quantity" => $quantity
            ];
        }
        return $panierData;
    }


    public function total(): float {
        #On calcule le total du panier ici, afin de ne pas a avoir a le faire dans la vue Twig
        $total = 0;


        foreach($this->showCart() as $item)
        {
            $total += $item['article']->getPrix() * $item['quantity'];
        }
        #On calcule le total du prix du produit * le nb de produits

        return $total;
    }


    public function add(int $id) {

          #ETAPE 1 : On récupere la session 'panier' si elle existe - sinon elle est créée avec un tableau vide
          $panier = $this->session->get('panier', []);

          #ETAPE 2 : On ajoute la quantité 1, au produit d'id $id
          if(!empty($panier[$id]))
          {
              $panier[$id]++;
          }
          else
          {
              $panier[$id] = 1;
          }
  
          #ETAPE 3 : On remplace la variable de session panier par le nouveau tableau $panier
          $this->session->set('panier', $panier);
  
    }

    public function remove(int $id) {

         #On récupere la session 'panier' si elle existe - sinon elle est créée avec un tableau vide
         $panier = $this->session->get('panier', []);
        
         #On supprime de la session celui dont on a passé l'id
         if(!empty($panier[$id]))
         {
             $panier[$id]--;
 
             if($panier[$id] <= 0)
             {
                 unset($panier[$id]); //unset pour dépiler de la session
             }
         }
 
         #On réaffecte le nouveau panier à la session
         $this->session->set('panier', $panier);
    } 

    public function clear() {
        $this->session->remove('panier'); 
    }
    
}