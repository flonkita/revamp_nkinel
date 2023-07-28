<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Regex;

class ContactInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' =>
                new Regex([
                'pattern' => '^[a-zA-Z0-9\s\'",.!@#%^&*()\-_+=;:<>\/]+$',
                'message' => 'Le nom ne doit contenir que des lettres, des chiffres et certains caractères spéciaux.',
                ])
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
            'constraints' =>
            new Regex([
                'pattern' => '^[a-zA-Z0-9\s\'",.!@#%^&*()\-_+=;:<>\/]+$',
                'message' => 'Le nom ne doit contenir que des lettres, des chiffres et certains caractères spéciaux.',
            ])

            ])
            ->add('message', TextType::class, [
                'label' => 'Message',
            'constraints' =>
            new Regex([
                'pattern' => '/^[^\[\]{}]*$/',
                'message' => 'Le message ne doit pas contenir de crochets ou d\'accolades.',
            ])

            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
