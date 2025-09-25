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

class CategoriesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Categories::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Supprimer l'action "delete" uniquement sur la page INDEX
            ->remove(Crud::PAGE_INDEX, Action::DELETE)

            ->add(Crud::PAGE_EDIT, Action::DELETE);  // ajoute sur détail
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom de la catégorie'),
            AssociationField::new('parent', 'Catégorie principale (laisser vide pour catégorie principale)')
                ->setCrudController(CategoriesCrudController::class)
                ->setRequired(false),
        ];
    }
}
