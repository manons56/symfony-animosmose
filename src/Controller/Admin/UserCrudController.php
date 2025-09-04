<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Address;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),  // l'ID n'est pas modifiable
            EmailField::new('email'),
            TextField::new('name', 'Nom'),
            TextField::new('surname', 'Prénom'),
            TextField::new('phone', 'Téléphone'),
            ArrayField::new('roles', 'Rôles')
                ->hideOnIndex(), // facultatif : tu peux voir les rôles seulement sur la fiche utilisateur

        // 👉 Panel séparé pour l'adresse
            FormField::addPanel('Adresse'),

            TextField::new('address.street', 'Rue'),
            TextField::new('address.city', 'Ville'),
            TextField::new('address.zipcode', 'Code Postal'),

        ];

        return $fields;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW); // désactive "Créer" dans la liste
    }
}
