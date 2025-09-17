<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class DeliveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $deliveryOptions = [
        'relay' => ['label' => 'Point relais', 'price' => 8.00],
        'home' => ['label' => 'Livraison à domicile', 'price' => 5.00],
        'pickup' => ['label' => 'Retrait sur place', 'price' => 0.00],
        ];

        $choices = [];
        foreach ($deliveryOptions as $key => $option) {
        $choices[$option['label'] . ' (+ ' . number_format($option['price'], 2) . ' €)'] = $key;
        }

        $builder
            ->add('delivery_method', ChoiceType::class, [
                'choices' => $choices,
                'expanded' => true,
                'multiple' => false,
                'label' => 'Choisissez votre mode de livraison',
                'choice_attr' => function($choice, $key, $value) use ($deliveryOptions)
                    {
                    return ['data-price' => $deliveryOptions[$value]['price']];
                    },
                ])
            ->add('cgv', CheckboxType::class, [
                'label' => 'J’ai lu et j’accepte les Conditions Générales de Vente',
                'mapped' => false, // on ne stocke pas en BDD
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions générales de vente pour continuer.',
                    ]),
                ],
            ]);
    }
}
