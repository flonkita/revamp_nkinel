<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'article',
                'required' => true,
                'error_bubbling' => true,
            ])
            ->add('prix', IntegerType::class, [
                'label' => 'Prix de l\'article',
                'required' => false,
                'error_bubbling' => true,
            ])
            ->add('stock')
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'article',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de l\'article',
                'label_attr' => [
                    'class' => 'mt-3 mb-3',
                ],
                'attr' => [
                    'class' => 'form-control-file',
                ],
                'mapped' => false,
                'required' => false,
                'error_bubbling' => true,
            ])            
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary mt-3',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
