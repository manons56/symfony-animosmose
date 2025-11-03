<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Nom'
                ],
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner votre nom',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit faire au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Prénom'
                ],
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner votre prénom',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom doit faire au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Numéro de téléphone'
                ],
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner votre téléphone',
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Le numéro doit faire au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Email'
                ],
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner votre email',
                    ]),
                    new Assert\Email([
                        'message' => 'Veuillez entrer une adresse email valide',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'Tapez votre message...',
                ],
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez écrire un message',
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Votre message doit contenir au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('website', HiddenType::class, [ // Invisible honeypot
                'attr' => ['style' => 'display:none;'],
                'mapped' => false,
                'required' => false,
            ])
            ->add('consent', CheckboxType::class, [
                'label' => 'J’accepte que mes données soient utilisées pour être recontacté(e).',
                'mapped' => false, // not linked to an entity property
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter le traitement de vos données pour envoyer le formulaire.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // the form is not linked to an entity
        ]);
    }
}
