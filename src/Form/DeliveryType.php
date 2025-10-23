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
            'pickup' => ['label' => 'Retrait sur place, sur RDV', 'price' => 0.00],
        ];

        $builder
            ->add('delivery_method', ChoiceType::class, [
                'choices' => [
                    'Point relais (+ 8 €)' => 'relay',
                    'Livraison à domicile (+ 5 €)' => 'home',
                    'Retrait sur place, sur RDV (+ 0 €)' => 'pickup',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Choisissez votre mode de livraison',
                'choice_attr' => function($choice, $key, $value) use ($deliveryOptions) {
                    return ['data-price' => $deliveryOptions[$value]['price']];
                },
            ])
            ->add('cgv', CheckboxType::class, [
                'label' => 'J’ai lu et j’accepte les Conditions Générales de Vente',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions générales de vente pour continuer.',
                    ]),
                ],
            ]);
    }
}
