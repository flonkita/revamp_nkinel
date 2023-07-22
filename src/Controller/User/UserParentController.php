<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class UserParentController extends AbstractController
{
    public function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        $user = $this->getUser();

        // Ajoute la variable "user" dans le contexte de toutes les vues
        $parameters['user'] = $user;

        return parent::render($view, $parameters, $response);
    }
}