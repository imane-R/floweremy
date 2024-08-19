<?php

namespace App\Form;

use App\Entity\Order;
use App\Enum\OrderStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('totalPrice', NumberType::class, [
                'label' => 'Prix total',
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut de la commande',
                'choices' => [
                    'En attente' => OrderStatus::PENDING,
                    'Annulée' => OrderStatus::CANCELLED,
                    'Confirmée' => OrderStatus::CONFIRMED,
                    'Complétée' => OrderStatus::COMPLETED,
                ],
                'choice_label' => function (?OrderStatus $status) {
                    return $status ? $status->value : '';
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
