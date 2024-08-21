<?php

namespace App\Form;

use App\Entity\LegalAndPolicy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LegalAndPolicyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('legalNotice', TextareaType::class, [
                'label' => 'Mentions légales',
                'attr' => [
                    'placeholder' => 'Entrez les mentions légales ici...',
                    'rows' => 10,
                ],
                'required' => false,
            ])
            ->add('confidentialPolicy', TextareaType::class, [
                'label' => 'Politique de confidentialité',
                'attr' => [
                    'placeholder' => 'Entrez la politique de confidentialité ici...',
                    'rows' => 10,
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LegalAndPolicy::class,
        ]);
    }
}
