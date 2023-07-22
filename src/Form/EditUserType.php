<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data'];

        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom de l\'utilisateur',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Prénom de l\'utilisateur',
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre email',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nouveau mot de passe',
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères',
                        // Vous pouvez ajuster la longueur minimale selon vos besoins
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmez le mot de passe',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Confirmez le mot de passe',
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez confirmer le mot de passe']),
                    
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary mt-3',
                ],
            ]);

        // Ajouter un événement pour vérifier la correspondance des mots de passe
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new FormError('Les mots de passe ne correspondent pas.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}