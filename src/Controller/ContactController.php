<?php

namespace App\Controller;

use App\Form\ContactInfoType;
use Symfony\Component\Mime\Address;
use App\Repository\ContactRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function index(Request $request, ContactRepository $contactRepository, MailerInterface $mailer, SessionInterface $session): Response
    {
        $contact = $contactRepository->findOneBy([]);
        $form = $this->createForm(ContactInfoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $email = (new TemplatedEmail())
                ->from(new Address($contact['email'], $contact['name']))
                ->to(new Address("test@gmail.com", "test"))
                // ->addCc(new Address('cyril.couvreur@cecop.com', 'Cyril Couvreur'))
                ->subject(' Nkinel :  Nouveau Contact')
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'contact' => $contact,
                ]);
            try {
                $mailer->send($email);
                $session->getFlashBag()->add('mail_info', 'Votre message a bien été envoyé, nous vous répondrons dans les plus brefs délais.');
            } catch (\Exception $e) {
                throw $e;

                $this->addFlash('error', 'Une erreur est survenue durant l\'envoie de l\'email de contact, veuillez réessayer ultérieurement ou nous contacter directement par email.');
                return $this->redirectToRoute('app_contact');
            }
        }
        return $this->render('contact/index.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }
}
