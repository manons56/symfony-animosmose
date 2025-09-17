<?php

namespace App\Form;

use App\Entity\Pictures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PicturesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('filename', FileType::class, [
            'label' => 'Image',
            'mapped' => false,
            'required' => false,
            'constraints' =>
                [
                    new File([
                    'maxSize' => '5M',
                    'mimeTypes' => ['image/*'],
                    'mimeTypesMessage' => 'Merci de télécharger une image valide',
                    ])
                ],
        ])
        ->add('isCover', CheckboxType::class, [
            'label' => 'Image principale',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class' => Pictures::class,
        ]);
    }
}
