<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Enum\OrderStatus;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * CRUD controller for managing Orders in the admin panel.
 * Allows admins to view, edit, and archive orders,
 * customize the index, detail, and edit pages, and filter out archived orders.
 */
class OrdersCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator; // Helps redirect easily to any admin page without manually coding routes
    private EntityManagerInterface $entityManager; // Used to update the database

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityManager = $entityManager;
    }

    // Specify which entity this CRUD controller manages
    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    // Configure labels and default sorting for CRUD pages
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->setDefaultSort(['date' => 'DESC']);
    }

    // Configure the actions available in the CRUD interface
    public function configureActions(Actions $actions): Actions
    {
        // Create a custom "Archive" action
        $archive = Action::new('archive', 'Archiver')
            ->linkToCrudAction('archiveOrder')
            ->setCssClass('btn btn-warning');

        return $actions
            // Rename the "Save and Continue" button on edit page
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $action) {
                return $action->setLabel('Enregistrer et continuer');
            })
            ->add(Crud::PAGE_INDEX, Action::DETAIL) // Keep the "Detail" action
            ->add(Crud::PAGE_INDEX, $archive)     // Add the "Archive" action
            ->remove(Crud::PAGE_INDEX, Action::NEW) // Disable "New" action in the list
            ->remove(Crud::PAGE_INDEX, Action::DELETE); // Disable "Delete" action in the list
    }

    // Custom action to archive an order
    public function archiveOrder(AdminContext $context): RedirectResponse
    {
        /** @var Orders $order */
        $order = $context->getEntity()->getInstance();
        $order->setArchived(true);

        // Persist changes to the database
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->addFlash('success', 'Commande archivée avec succès !');

        // Redirect back to the index page
        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    // Customize the query for the index page to exclude archived orders
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.archived = :archived')
            ->setParameter('archived', false);

        return $qb;
    }

    // Configure fields for different pages (INDEX, DETAIL, EDIT)
    public function configureFields(string $pageName): iterable
    {
        // Prepare choices for order status
        $statusChoices = [];
        foreach (OrderStatus::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case; // label => value
        }

        // === INDEX PAGE ===
        if ($pageName === Crud::PAGE_INDEX) {
            return [
                AssociationField::new('user', 'Client'), // Client
                DateTimeField::new('date', 'Date'), // Order date
                MoneyField::new('totalWithDelivery', 'Total')->setCurrency('EUR')->setStoredAsCents(false), // Total amount
                ChoiceField::new('status', 'Statut')
                    ->setChoices($statusChoices)
                    ->renderAsBadges([ // Display status as colored badges
                        OrderStatus::Pending->value => 'warning',
                        OrderStatus::Paid->value => 'success',
                        OrderStatus::Failed->value => 'danger',
                        OrderStatus::Delivered->value => 'info',
                        OrderStatus::Canceled->value => 'secondary',
                    ]),
                TextField::new('deliveryMethod', 'Livraison') // Delivery method
                ->formatValue(function ($value) {
                    return match ($value) {
                        'relay' => 'Mondial Relay',
                        'home' => 'Livraison à domicile',
                        'pickup' => 'Retrait sur place',
                        default => ucfirst((string)$value),
                    };
                }),
            ];
        }

        // === DETAIL PAGE ===
        if ($pageName === Crud::PAGE_DETAIL) {
            return [
                AssociationField::new('user', 'Client'),
                AssociationField::new('address_id', 'Adresse de livraison')->onlyOnDetail(),
                DateTimeField::new('date', 'Date'),
                ChoiceField::new('status', 'Statut')
                    ->setChoices($statusChoices)
                    ->renderExpanded(false)
                    ->renderAsBadges([
                        OrderStatus::Pending->value => 'warning',
                        OrderStatus::Paid->value => 'success',
                        OrderStatus::Failed->value => 'danger',
                        OrderStatus::Delivered->value => 'info',
                        OrderStatus::Canceled->value => 'secondary',
                    ]),
                CollectionField::new('articles', 'Articles commandés')
                    ->setTemplatePath('admin/order_articles.html.twig'),
                TextField::new('deliveryMethod', 'Livraison')
                    ->formatValue(function ($value) {
                        return match ($value) {
                            'relay' => 'Mondial Relay',
                            'home' => 'Livraison à domicile',
                            'pickup' => 'Retrait sur place',
                            default => ucfirst((string)$value),
                        };
                    }),
                MoneyField::new('totalWithDelivery', 'Total')->setCurrency('EUR')->setStoredAsCents(false),
            ];
        }

        // === EDIT PAGE ===
        if ($pageName === Crud::PAGE_EDIT) {
            return [
                ChoiceField::new('status', 'Statut')
                    ->setChoices($statusChoices)
                    ->renderExpanded(false)
                    ->renderAsBadges([
                        OrderStatus::Pending->value => 'warning',
                        OrderStatus::Paid->value => 'success',
                        OrderStatus::Failed->value => 'danger',
                        OrderStatus::Delivered->value => 'info',
                        OrderStatus::Canceled->value => 'secondary',
                    ]), // Allows changing the status
            ];
        }
    }
}
