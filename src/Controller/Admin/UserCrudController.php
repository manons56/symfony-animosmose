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

/**
 * CRUD controller for managing users in the EasyAdmin backend.
 * Includes embedded address fields for each user.
 */
class UserCrudController extends AbstractCrudController
{
    /**
     * Returns the fully qualified class name of the entity managed by this CRUD controller.
     */
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Configure the fields displayed in EasyAdmin forms and pages.
     *
     * @param string $pageName The current page (index, detail, edit, new)
     * @return iterable List of fields
     */
    public function configureFields(string $pageName): iterable
    {
        $fields = [
            // Auto-increment ID, not editable in forms
            IdField::new('id')->hideOnForm(),

            // User email field
            EmailField::new('email'),

            // User personal details
            TextField::new('name', 'Nom'),
            TextField::new('surname', 'Prénom'),
            TextField::new('phone', 'Téléphone'),

            // Roles (array), hidden in index to keep table clean
            ArrayField::new('roles', 'Rôles')->hideOnIndex(),

            //  Separate panel for address fields
            FormField::addPanel('Adresse'),

            // Embedded address fields
            TextField::new('address.street', 'Rue'),
            TextField::new('address.city', 'Ville'),
            TextField::new('address.zipcode', 'Code Postal'),
        ];

        return $fields;
    }

    /**
     * Configure available actions for the CRUD pages.
     *
     * @param Actions $actions
     * @return Actions Modified actions object
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Disable the "New" button on the index page
            ->remove(Crud::PAGE_INDEX, Action::NEW)

            // Remove "Delete" button on index page (to prevent accidental deletion)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)

            // Allow "Delete" action only on the edit/detail page
            ->add(Crud::PAGE_EDIT, Action::DELETE);
    }
}
