<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

/**
 * This CRUD controller manages product categories in the admin panel.
 * It allows administrators to view, edit, and delete categories,
 * while controlling where the "delete" action is available.
 */
class CategoriesCrudController extends AbstractCrudController
{
    // Specifies which entity this CRUD controller manages
    public static function getEntityFqcn(): string
    {
        return Categories::class;
    }

    // Configure the actions available in the CRUD interface
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Remove the "delete" action only from the INDEX (list) page
            ->remove(Crud::PAGE_INDEX, Action::DELETE)

            ->add(Crud::PAGE_EDIT, Action::DELETE);  // Add it on the EDIT page
    }

    // Configure which fields are displayed on CRUD pages
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), // Hide the ID field in forms
            TextField::new('name', 'Nom de la catégorie'), // Category name
            AssociationField::new('parent', 'Catégorie principale (laisser vide pour catégorie principale)') // Parent category
            ->setCrudController(CategoriesCrudController::class)
                ->setRequired(false),
        ];
    }
}
