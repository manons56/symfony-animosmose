<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Variants;
use App\Entity\Picture;
use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EntityField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('label', 'Nom du produit'),

            MoneyField::new('price', 'Prix')->setCurrency('EUR'),

            BooleanField::new('isNew', 'Nouveau'),
            BooleanField::new('isBestSeller', 'Best Seller'),

            // Variants, en OneToMany
            AssociationField::new('variants', 'Variantes') //EasyAdmin va générer automatiquement les formulaires et les menus pour chaque association grâce aux AssociationField.
                ->setCrudController(VariantsCrudController::class)
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),


            // Pictures, en OneToOne, EntityField suffit, AssociationField est nécessaire quand y'a une relation avec un Many
            EntityField::new('picture', 'Image'),



            // Category, en ManyToOne
            AssociationField::new('category', 'Catégorie')
                ->setCrudController(CategoryCrudController::class),
        ];
    }
}




//by_reference => false pour variants :
//Nécessaire si tu veux ajouter/supprimer des entités liées directement depuis le formulaire du produit.
//variants c'est souvent des collections (OneToMany ou ManyToMany) : un produit peut avoir plusieurs variants
//category est généralement ManyToOne : un produit n’a qu’une seule catégorie. Idem pour pictures qui est en OneToOne avec Products
//Pour les collections, si tu veux ajouter ou supprimer des éléments directement dans le formulaire, il faut by_reference => false.
// Pour une relation simple (ManyToOne), Symfony crée automatiquement l’entité liée via un select dans le formulaire, donc by_reference => false n’est pas nécessaire.

