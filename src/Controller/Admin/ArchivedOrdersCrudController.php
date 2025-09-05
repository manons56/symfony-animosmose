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

class ArchivedOrdersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande archivée')
            ->setEntityLabelInPlural('Commandes archivées')
            ->setDefaultSort(['date' => 'DESC']);
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        // On ne récupère que les commandes archivées
        $qb->andWhere('entity.archived = :archived')
            ->setParameter('archived', true);

        return $qb;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW); // désactive "Créer" dans la liste
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_INDEX) {
            return [
                AssociationField::new('user', 'Client'),
                DateTimeField::new('date', 'Date'),
                MoneyField::new('total', 'Total')->setCurrency('EUR')->setStoredAsCents(false),
            ];
        }

        if ($pageName === Crud::PAGE_DETAIL) {
            return [
                AssociationField::new('user', 'Client'),
                AssociationField::new('address', 'Adresse de livraison')->onlyOnDetail(),
                DateTimeField::new('date', 'Date'),
                CollectionField::new('articles', 'Articles commandés')
                    ->setTemplatePath('admin/order_articles.html.twig'),
                MoneyField::new('total', 'Total')->setCurrency('EUR')->setStoredAsCents(false),
            ];
        }
    }
}
