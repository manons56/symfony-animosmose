<?php

namespace App\Controller\Admin;

use App\Entity\Pictures;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PicturesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        // Define the entity that this CRUD controller manages
        return Pictures::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // Configure the fields to be displayed in the EasyAdmin backend
        return [
            // ID field: hidden in forms, shown in index/detail pages
            IdField::new('id')->hideOnForm(),

            // Path field: displays the image path, label kept in French
            TextField::new('path', 'Chemin image'),
        ];
    }
}
