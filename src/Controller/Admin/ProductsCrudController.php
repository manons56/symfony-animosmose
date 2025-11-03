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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class ProductsCrudController extends AbstractCrudController
{
    private CategoriesRepository $categoriesRepo;

    public function __construct(CategoriesRepository $categoriesRepo)
    {
        // Inject the categories repository for use in category-related fields
        $this->categoriesRepo = $categoriesRepo;
    }

    public static function getEntityFqcn(): string
    {
        // Define the entity that this CRUD controller manages
        return Products::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Configure available actions
        return $actions
            // Remove the "delete" action from the index page (list of products)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            // Add the delete action on the detail page
            ->add(Crud::PAGE_EDIT, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        // --- Images gallery field ---
        $imagesCollectionField = CollectionField::new('images', 'Galerie d\'images')
            ->allowAdd()                              // allow adding new images
            ->allowDelete()                           // allow deleting images
            ->setEntryType(PicturesType::class)       // specify the form type for each image
            ->setFormTypeOptions(['by_reference' => false]) // ensures Doctrine handles collection properly
            ->onlyOnForms();                          // visible only on new/edit forms

        // --- Variants collection field ---
        $variantsFields = CollectionField::new('variants', 'Variants')
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(VariantType::class)        // form type for each variant
            ->setFormTypeOptions(['by_reference' => false]);

        // --- Category / Subcategory field ---
        $categoryField = AssociationField::new('category', 'Catégorie / Sous-catégorie')
            ->setCrudController(CategoriesCrudController::class)
            ->setFormTypeOption('choice_label', 'name') // display category name
            ->setFormTypeOption('group_by', function($cat) {
                // Group subcategories under their parent
                return $cat->getParent() ? $cat->getParent()->getName() : 'Catégorie principale';
            })
            ->setRequired(true);

        // --- Common fields for new/edit/detail pages ---
        $commonFields = [
            TextField::new('name', 'Nom du produit'), // product name
            BooleanField::new('isNew', 'Nouveau')    // product is new flag
            ->setLabel('Nouveau')
                ->onlyOnForms(),                      // visible only in forms
            BooleanField::new('isNew', 'Nouveau')    // display as badge on index
            ->renderAsSwitch(false)
                ->formatValue(function ($value, $entity) {
                    return $value ? '<span class="badge bg-success">Nouveau</span>' : '';
                })
                ->onlyOnIndex(),
            BooleanField::new('isBestSeller', 'Best Seller'),       // bestseller flag
            BooleanField::new('isOutOfStock', 'Rupture de stock'),  // out of stock flag
            BooleanField::new('isCustomizable', 'Personnalisable')  // customizable flag
            ->setLabel('Personnalisable')
                ->onlyOnForms(),
            BooleanField::new('isCustomizable', 'Personnalisable')  // display as badge on index
            ->renderAsSwitch(false)
                ->formatValue(function ($value, $entity) {
                    return $value ? '<span class="badge bg-info">Personnalisable</span>' : '';
                })
                ->onlyOnIndex(),

            $imagesCollectionField,  // image gallery
            $categoryField,          // category/subcategory
            $variantsFields,         // product variants
            TextEditorField::new('description', 'Description du produit'), // rich text description
            TextField::new('composition', 'Composition'),
            TextField::new('analytics_components', 'Composants analytiques'),
            TextField::new('nutritionnal_additive', 'Additifs'),
        ];

        // --- Index page: show only product name ---
        if ($pageName === Crud::PAGE_INDEX) {
            return [TextField::new('name', 'Nom du produit')];
        }

        // --- Detail page: show common fields + ID ---
        if ($pageName === Crud::PAGE_DETAIL) {
            return array_merge([IdField::new('id')->hideOnForm()], $commonFields);
        }

        // --- New/Edit page: show all common fields ---
        return $commonFields;
    }

    // --- Handle persistence of uploaded files ---
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Products) return;

        // Ensure at least one image exists
        if (count($entityInstance->getImages()) === 0) {
            throw new \InvalidArgumentException('Le produit doit contenir au moins une image.');
        }

        // Loop through uploaded files in the images collection
        foreach ($entityInstance->getImages() as $image) {
            $file = $image->getFile();
            if ($file instanceof UploadedFile) {
                // Generate unique filename and move to products images directory
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('products_images_directory'), $newFilename);
                $image->setFilename($newFilename);
            }
        }

        // Call parent to persist entity
        parent::persistEntity($entityManager, $entityInstance);
    }

    // --- Update entity (reuse persistEntity logic) ---
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->persistEntity($entityManager, $entityInstance);
    }

    // --- Include additional JS assets in admin pages ---
    public function configureAssets(Assets $assets): Assets
    {
        return $assets->addJsFile('js/admin-products.js');
    }
}
