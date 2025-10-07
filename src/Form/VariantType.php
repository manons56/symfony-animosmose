<?php

namespace App\Form;

use App\Entity\Variants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class VariantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenance', TextType::class, [
                'required' => false,
                'label' => 'Contenance',
            ])
            ->add('size', TextType::class, [
                'required' => false,
                'label' => 'Taille',
            ])
            ->add('color', TextType::class, [
                'required' => false,
                'label' => 'Couleur',
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix (€)',
                'currency' => 'EUR'
            ])
            ->add('isDefault', CheckboxType::class, [
                'required' => false,
                'label' => 'Par défaut ?',
            ])
            ->add('isOutOfStock', CheckboxType::class, [
            'required' => false,
            'label' => 'Indisponible',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Variants::class,
        ]);
    }
}
