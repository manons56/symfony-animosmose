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
            ->add('label', TextType::class, [
                'label' => 'Label du variant',
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du variant',
                'currency' => 'EUR'
            ])
            ->add('isDefault', CheckboxType::class, [
                'required' => false,
                'label' => 'Par défaut ?',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Variants::class,
        ]);
    }
}
