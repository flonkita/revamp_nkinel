<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/admin/user", name="admin_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, ManagerRegistry $doctrine,UserRepository $userRepository, SessionInterface $session, Security $security): Response
    {
        $user = $security->getUser();
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $userRepository->add($user, true);

            $session->getFlashBag()->add('edit_user', 'Cet user a bien été modifié.');
            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'user' => $user,
            'formUser' => $formUser,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function delete($id, User $user, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
         #Etape 1 : On appelle l'entity manager de doctrine
         $entityManager = $doctrine->getManager();

         #Etape 2 : On récupère (grâce au repository de doctrine) l'objet que l'on souhaite modifier
         $user = $doctrine->getRepository(User::class)->find($id);

         #Etape 3 : On supprime à l'aide de l'entity manager 
         $entityManager->remove($user);

         #Etape 4 : On valide les modifications
         $entityManager->flush();

         $session->getFlashBag()->add('delete_user', 'Cet user a bien été supprimé.');

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
