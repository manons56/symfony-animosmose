<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Entity\Variants;
use App\Entity\Pictures;
use App\Entity\Categories;
use App\Form\VariantType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
//use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;



class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureFields(string $pageName): iterable
    {
        /*$imageField = ImageField::new('image', 'Image') //on utilise maintenant le champ 'image' dans Products pour gérer l’image.
        ->setBasePath('img/products') // le chemin relatif que EasyAdmin utilisera pour afficher l’image dans la liste ou le détail, depuis /public
        ->setUploadDir('public/img/products') //dossier physique où les images seront uploadées si tu ajoutes un formulaire d’édition.
        ->setRequired(false) //pour ne pas forcer la présence d’une image sur tous les produits.
        ->setLabel('Image du produit');
        */


        $variantsFields = CollectionField::new('variants', 'Variants')
            ->allowAdd()      // autorise l'ajout direct
            ->allowDelete()   // autorise la suppression
            ->setEntryType(VariantType::class) // formulaire utilisé pour chaque variant
            ->setFormTypeOptions([
                'by_reference' => false, // important pour gérer l'ajout/suppression correctement
            ]);



        // Champs communs à new, edit, detail
        $commonFields = [
            TextField::new('name', 'Nom du produit'),
            TextField::new('description', 'Description du produit'),
            TextField::new('composition', 'Composition'),
            TextField::new('analytics_components', 'Composants analytiques'),
            TextField::new('nutritionnal_additive', 'Additifs'),

            BooleanField::new('isNew', 'Nouveau'),
            BooleanField::new('isBestSeller', 'Best Seller'),


            $variantsFields,
           // $imageField, // Image du produit directement dans Products

            // Category, en ManyToOne
            AssociationField::new('category_id', 'Catégorie')
                ->setCrudController(CategoriesCrudController::class),
        ];

        if ($pageName === Crud::PAGE_INDEX) {
            return [
                TextField::new('name', 'Nom du produit'),
            ];
        }

        if ($pageName === Crud::PAGE_DETAIL) {
            return array_merge([
                IdField::new('id')->hideOnForm(),
            ], $commonFields);
        }

        // new & edit
        return $commonFields;
    }
}




//by_reference => false pour variants :
//Nécessaire pour ajouter/supprimer des entités liées directement depuis le formulaire du produit.
//variants c'est souvent des collections (OneToMany ou ManyToMany) : un produit peut avoir plusieurs variants
//category est généralement ManyToOne : un produit n’a qu’une seule catégorie. Idem pour pictures qui est en OneToOne avec Products
//Pour les collections, si tu veux ajouter ou supprimer des éléments directement dans le formulaire, il faut by_reference => false.
// Pour une relation simple (ManyToOne), Symfony crée automatiquement l’entité liée via un select dans le formulaire, donc by_reference => false n’est pas nécessaire.

