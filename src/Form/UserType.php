<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;

        // En mode édition, on ne montre pas le mot de passe ni la certification
        if (!$isEdit) {
            $builder
                ->add('plainPassword', PasswordType::class, [
                    'mapped' => false,
                    'label' => 'Mot de passe',
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Veuillez saisir un mot de passe',
                        ]),
                        new Assert\Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                            'max' => 32,
                            'maxMessage' => 'Votre mot de passe ne peut pas dépasser {{ limit }} caractères',
                        ]),
                        new Assert\Regex([
                            'pattern' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.*\s).{8,32}$/',
                            'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
                        ]),
                    ],
                ])
                ->add('certification', CheckboxType::class, [
                    'mapped' => false,
                    'label' => "Je certifie l'exactitude des informations fournies",
                    'constraints' => [
                        new Assert\IsTrue(message: 'Vous devez cocher la case pour valider votre inscription'),
                    ],
                ]);
        }

        //  Ces champs sont communs à l'inscription ET à la modification
        $builder
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('phone', TextType::class)
            ->add('email', EmailType::class)
            ->add('address', AddressType::class, [
                'label' => false, // masque le label "Adresse" automatique
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false, // par défaut, c’est une inscription
        ]);
    }
}
