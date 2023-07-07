<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin", name="admin_")
 */
class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="index_contact")
     */
    public function index(Request $request, SessionInterface $session): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contact = $entityManager->getRepository(Contact::class)->findOneBy([]);

        if (!$contact) {
            $contact = new Contact();
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $entityManager->persist($data);
            $entityManager->flush();
            $session->getFlashBag()->add('edit_contact', 'La page contact a bien été modifié.');
            return $this->redirectToRoute('admin_index_contact');
        }

        return $this->render('admin/contact/index.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact
        ]);
    }
}
