<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Controller\User\UserParentController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\EditUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 */
class UserController extends UserParentController
{
    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"}, requirements={"id":"\d+"})
     * @ParamConverter("user", class="App\Entity\User")
     */
    public function edit(Request $request,SessionInterface $session, User $user, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager): Response
{
    $formUser = $this->createForm(EditUserType::class, $user);
    $formUser->handleRequest($request);

    if ($formUser->isSubmitted() && $formUser->isValid()) {
        // Vérification et mise à jour du mot de passe si modifié
        $plainPassword = $formUser->get('plainPassword')->getData();
        if ($plainPassword !== null) {
            $encodedPassword = $passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
        }

        $entityManager->flush();

        $session->getFlashBag()->add('edit_user', 'Vos informations ont été modifiées avec succès.');

        return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
    }

    return $this->renderForm('user/edit.html.twig', [
        'user' => $user,
        'formUser' => $formUser,
    ]);
}

}
