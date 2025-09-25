<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\VariantType;
use App\Repository\CategoriesRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use App\Form\PicturesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;




class ProductsCrudController extends AbstractCrudController
{
    private CategoriesRepository $categoriesRepo;

    public function __construct(CategoriesRepository $categoriesRepo)
    {
        $this->categoriesRepo = $categoriesRepo;
    }

    public static function getEntityFqcn(): string
    {
        return Products::class;
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
        // Champ image unique (pour la photo principale)
        $imageField = ImageField::new('image', 'Image')
            ->setBasePath('img/products')            // chemin pour afficher l'image
            ->setUploadDir('public/img/products')    // dossier où uploader
            ->setRequired(false)                      // non obligatoire
            ->setLabel('Image du produit');

        // Champ galerie d'images (plusieurs images)
        $imagesCollectionField = CollectionField::new('images', 'Galerie d\'images')
            ->allowAdd()                              // autoriser l'ajout
            ->allowDelete()                           // autoriser la suppression
            ->setEntryType(PicturesType::class)   // formulaire pour chaque image
            ->setFormTypeOptions(['by_reference' => false]) // pour que Doctrine gère correctement la collection
            ->onlyOnForms();                          // affiché uniquement dans les formulaires (new/edit)

        // Champ variants (collection)
        $variantsFields = CollectionField::new('variants', 'Variants')
            ->allowAdd()                              // autoriser l'ajout
            ->allowDelete()                           // autoriser la suppression
            ->setEntryType(VariantType::class)        // type de formulaire pour chaque variant
            ->setFormTypeOptions(['by_reference' => false]);

        // Champ catégorie / sous-catégorie
        $categoryField = AssociationField::new('category', 'Catégorie / Sous-catégorie')
            ->setCrudController(CategoriesCrudController::class)
            ->setFormTypeOption('choice_label', 'name')
            ->setFormTypeOption('group_by', function($cat) {
                return $cat->getParent() ? $cat->getParent()->getName() : 'Catégorie principale';
            })
            ->setRequired(true);

        // Champs communs pour formulaire new/edit/detail
        $commonFields = [
            TextField::new('name', 'Nom du produit'),
            TextField::new('capacity', 'Contenance')
                ->setRequired(false)           // le champ n'est plus obligatoire
                ->setFormTypeOption('empty_data', null), // si vide, Symfony met null
            TextField::new('description', 'Description du produit'),
            TextField::new('composition', 'Composition'),
            TextField::new('analytics_components', 'Composants analytiques'),
            TextField::new('nutritionnal_additive', 'Additifs'),
            BooleanField::new('isNew', 'Nouveau')// Nouveau / formulaire
                ->setLabel('Nouveau')      // facultatif, juste pour être clair
                ->onlyOnForms(),            // visible uniquement dans new/edit

            // Liste (index)
            BooleanField::new('isNew', 'Nouveau')
                ->renderAsSwitch(false)
                ->formatValue(function ($value, $entity) {
                    return $value ? '<span class="badge bg-success">Nouveau</span>' : '';
                })
                ->onlyOnIndex(),
            BooleanField::new('isBestSeller', 'Best Seller'),
            BooleanField::new('isOutOfStock', 'Rupture de stock'),
            $imageField,            // Image unique (optionnelle)
            $imagesCollectionField, // Galerie multiple
            $variantsFields,
            $categoryField,
        ];

        // Page index : on n'affiche que le nom du produit
        if ($pageName === Crud::PAGE_INDEX) {
            return [TextField::new('name', 'Nom du produit')];
        }

        // Page détail : on affiche les champs communs + id
        if ($pageName === Crud::PAGE_DETAIL) {
            return array_merge([IdField::new('id')->hideOnForm()], $commonFields);
        }

        // Page new/edit : on affiche tous les champs communs
        return $commonFields;
    }


//Sans PersistEntity :
//Dans  PicturesType,  utilisation d'un FileType pour le champ filename.
//Symfony ne va pas automatiquement déplacer le fichier uploadé dans le dossier public/img/products.
//Au moment de persister l’entité, Doctrine va juste stocker l’objet UploadedFile ou la valeur vide dans la base, selon la configuration.
//Résultat : les fichiers ne sont pas réellement sauvegardés, ou le nom du fichier n’est pas enregistré correctement dans la table ProductImage.
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Products) return;

        foreach ($entityInstance->getImages() as $image) {
            $file = $image->getFilename(); // Le FileType retourne un UploadedFile
            if ($file instanceof UploadedFile) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('products_images_directory'), $newFilename);
                $image->setFilename($newFilename);
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->persistEntity($entityManager, $entityInstance);
    }


    public function configureAssets(Assets $assets): Assets
    {
        return $assets->addJsFile('js/admin-products.js');
    }
}
