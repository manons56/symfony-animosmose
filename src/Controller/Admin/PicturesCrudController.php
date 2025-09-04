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
        return Pictures::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('path', 'Chemin image'),
        ];
    }
}
