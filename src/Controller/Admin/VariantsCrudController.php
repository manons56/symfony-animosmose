<?php

namespace App\Controller\Admin;

use App\Entity\Variants;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class VariantsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Variants::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('contenance', 'Contenance')->hideOnIndex(),
            TextField::new('size', 'Taille')->hideOnIndex(),
            TextField::new('color', 'Couleur')->hideOnIndex(),
            NumberField::new('price', 'Prix (€)')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setNumDecimals(2),
            BooleanField::new('isDefault', 'Par défaut'),
        ];
    }
}
