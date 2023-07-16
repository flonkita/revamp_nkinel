<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator)
    {

        $pagination = $paginator->paginate(
            $articleRepository->paginationQuery(),
            $request->query->get('page', 1),
            3
        );

        return $this->render('home/index.html.twig', [
            "pagination" => $pagination,
        ]);
    }
}
