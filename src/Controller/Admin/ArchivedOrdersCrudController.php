<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

/**
 * This CRUD controller manages archived orders in the admin panel.
 * It allows administrators to view a list of orders that have been archived,
 * see details of each order, and ensures that no new orders can be created from this interface.
 * The controller customizes the list and detail views and filters only archived orders.
 */
class ArchivedOrdersCrudController extends AbstractCrudController
{
    // Specifies which entity this CRUD controller manages
    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    // Configures labels and default sorting for the CRUD pages
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande archivée') // Singular label
            ->setEntityLabelInPlural('Commandes archivées') // Plural label
            ->setDefaultSort(['date' => 'DESC']); // Sort archived orders by date descending
    }

    // Customize the query used for the index (list) page
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        // Get the default query builder from the parent
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Filter the results to only include archived orders
        $qb->andWhere('entity.archived = :archived')
            ->setParameter('archived', true);

        return $qb;
    }

    // Configure the actions available in the CRUD interface
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW); // Disable the "New" action on the index page
    }

    // Configure which fields are displayed on different CRUD pages
    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_INDEX) {
            // Fields displayed in the list view
            return [
                AssociationField::new('user', 'Client'), // Shows the user who made the order
                DateTimeField::new('date', 'Date'), // Order date
                MoneyField::new('total', 'Total')->setCurrency('EUR')->setStoredAsCents(false), // Total amount
            ];
        }

        if ($pageName === Crud::PAGE_DETAIL) {
            // Fields displayed on the detail view
            return [
                AssociationField::new('user', 'Client'), // Client
                AssociationField::new('address', 'Adresse de livraison')->onlyOnDetail(), // Delivery address (detail only)
                DateTimeField::new('date', 'Date'), // Order date
                CollectionField::new('articles', 'Articles commandés') // Ordered articles
                ->setTemplatePath('admin/order_articles.html.twig'), // Use custom template for articles
                MoneyField::new('total', 'Total')->setCurrency('EUR')->setStoredAsCents(false), // Total amount
            ];
        }
    }
}
