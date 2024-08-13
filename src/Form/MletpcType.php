<?php

namespace App\Form;

use App\Entity\Mletpc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MletpcType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mentionslegales')
            ->add('politiquesdeconfidentialite')
            ->add('envoyer', SubmitType::class, ['attr' => [
                'class' => 'buttonForm'
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mletpc::class,
        ]);
    }
}
