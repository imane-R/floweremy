<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder' => 'Entrez le nom du produit',
                    'class' => 'form-control'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
                'attr' => [
                    'placeholder' => 'Entrez le prix',
                    'class' => 'form-control'
                ]
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'attr' => [
                    'placeholder' => 'Entrez la quantité en stock',
                    'class' => 'form-control',
                    'min' => 0
                ]
            ])
            ->add('color', TextType::class, [
                'label' => 'Couleur',
                'attr' => [
                    'placeholder' => 'Entrez la couleur',
                    'class' => 'form-control'
                ]
            ])
            ->add('size', TextType::class, [
                'label' => 'Taille',
                'attr' => [
                    'placeholder' => 'Entrez la taille',
                    'class' => 'form-control'
                ]
            ])
            ->add('weight', NumberType::class, [
                'label' => 'Poids (kg)',
                'attr' => [
                    'placeholder' => 'Entrez le poids en kilogrammes',
                    'class' => 'form-control',
                    'step' => 0.01,
                    'min' => 0
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Entrez une description détaillée',
                    'class' => 'form-control',
                    'rows' => 5
                ]
            ])
            ->add('imageForm', FileType::class, [
                'label' => 'Ajouter une image',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control-file'
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez une catégorie',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('envoyer', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-primary mt-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
