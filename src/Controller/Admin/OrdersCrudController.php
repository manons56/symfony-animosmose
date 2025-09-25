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



class OrdersCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator; //AdminUrlGenerator te permet de rediriger facilement vers n’importe quelle page de l’admin, sans avoir à coder les routes à la main
    private EntityManagerInterface $entityManager;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityManager = $entityManager; // pour màj de la bdd
    }

    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->setDefaultSort(['date' => 'DESC']);
    }

    // === La méthode updateStatus redirige simplement vers l'index ===
    /*public function updateStatus(Orders $order): Response
    {
        // On crée un message pour l'utilisateur.
        $this->addFlash('info', 'La commande n\'a pas été mise à jour.');

        // Et on redirige vers la page d'index
        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }
    */

    public function configureActions(Actions $actions): Actions
    {
        $archive = Action::new('archive', 'Archiver')
            ->linkToCrudAction('archiveOrder')
            ->setCssClass('btn btn-warning');

        return $actions
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $action) {
                return $action->setLabel('Enregistrer et continuer');
            })
            ->add(Crud::PAGE_INDEX, Action::DETAIL) // on garde l’action Détails
            ->add(Crud::PAGE_INDEX, $archive)     // on ajoute l’action Archiver
            ->remove(Crud::PAGE_INDEX, Action::NEW) // désactive "Créer" dans la liste
            ->remove(Crud::PAGE_INDEX, Action::DELETE); // désactive "Supprimer" dans la liste

    }




    public function archiveOrder(AdminContext $context): RedirectResponse
    {
        /** @var Orders $order */
        $order = $context->getEntity()->getInstance();
        $order->setArchived(true);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->addFlash('success', 'Commande archivée avec succès !');

        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }



    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.archived = :archived')
            ->setParameter('archived', false);

        return $qb;
    }


    public function configureFields(string $pageName): iterable
    {
        $statusChoices = [];
        foreach (OrderStatus::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case; // label => valeur
        }

        // === INDEX ===
        if ($pageName === Crud::PAGE_INDEX) {
            return [
                AssociationField::new('user', 'Client'),
                DateTimeField::new('date', 'Date'),
                MoneyField::new('total', 'Total')->setCurrency('EUR')
                    ->setStoredAsCents(false),
                ChoiceField::new('status', 'Statut')
                    ->setChoices($statusChoices)
                    ->renderAsBadges([
                        OrderStatus::Pending->value => 'warning',
                        OrderStatus::Paid->value => 'success',
                        OrderStatus::Failed->value => 'danger',
                        OrderStatus::Delivered->value => 'info',
                        OrderStatus::Canceled->value => 'secondary',
                    ]),
                TextField::new('deliveryMethod', 'Livraison')
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

        // === DETAIL ===
        if ($pageName === Crud::PAGE_DETAIL) {
            return [
                AssociationField::new('user', 'Client'),
                AssociationField::new('address_id', 'Adresse de livraison')->onlyOnDetail(),
                DateTimeField::new('date', 'Date'),
                ChoiceField::new('status', 'Statut')
                    ->setChoices($statusChoices)
                    ->renderExpanded(false) // menu déroulant
                    ->renderAsBadges([
                        OrderStatus::Pending->value => 'warning',
                        OrderStatus::Paid->value => 'success',
                        OrderStatus::Failed->value => 'danger',
                        OrderStatus::Delivered->value => 'info',
                        OrderStatus::Canceled->value => 'secondary',
                    ]),
                CollectionField::new('articles', 'Articles commandés')
                    ->setTemplatePath('admin/order_articles.html.twig'),
                MoneyField::new('total', 'Total')->setCurrency('EUR')
                    ->setStoredAsCents(false),
                TextField::new('deliveryMethod', 'Livraison')
                    ->formatValue(function ($value) {
                        return match ($value) {
                            'relay' => 'Mondial Relay',
                            'home' => 'Livraison à domicile',
                            'pickup' => 'Retrait sur place',
                            default => ucfirst((string)$value), //default => ... : c'est ce que le code retournera si aucune des valeurs ne correspond (relay, home, pickup).
                        };
                    }),

            ];
        }

        // === EDIT ===
        if ($pageName === Crud::PAGE_EDIT) {
            return [
                ChoiceField::new('status', 'Statut')
                    ->setChoices($statusChoices)
                    ->renderExpanded(false) // menu déroulant
                    ->renderAsBadges([
                        OrderStatus::Pending->value => 'warning',
                        OrderStatus::Paid->value => 'success',
                        OrderStatus::Failed->value => 'danger',
                        OrderStatus::Delivered->value => 'info',
                        OrderStatus::Canceled->value => 'secondary',
                    ]), // permet de modifier le statut
            ];
        }
    }

}
