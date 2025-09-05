<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;
use function Sodium\add;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2])
                ]
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2])
                ],
            ])

            ->add('telephone', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 10])
                ],
            ])


            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])

            ->add('message', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 10])
                ],
            ])


            ->add('envoyer', SubmitType::class) //Les validations ne concernent que les champs où l’utilisateur saisit des données.

            //champ honeypot invisible pour bots
            //Les vrais utilisateurs ne voient pas ce champ dans le formulaire, donc ils le laissent vide.
            //Les bots automatisés remplissent souvent tous les champs présents dans le HTML, même cachés.
            //Si le champ contient une valeur lors de la soumission → on considère que c’est un bot et on rejette la soumission.

            ->add('website', HiddenType::class, [ //"website" c’est juste un nom, souvent choisi pour ressembler à un champ normal qu’un bot pourrait remplir.
                'mapped' => false, //ce champ n’est pas lié à une propriété de l'entité.
                'required' => false, //ce champ n’est pas obligatoire côté formulaire.
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }


}
