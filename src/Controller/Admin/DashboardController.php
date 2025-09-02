<?php

namespace App\Controller\Admin;


use App\Entity\User;
use App\Entity\Products;
use App\Entity\Variants;
use App\Entity\Pictures;
use App\Entity\Categories;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $url = $adminUrlGenerator->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Back-Office Boutique');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        // Gestion des utilisateurs
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);

        // Gestion des produits
        yield MenuItem::linkToCrud('Produits', 'fas fa-box', Products::class);

        // Gestion des variantes
        yield MenuItem::linkToCrud('Variantes', 'fas fa-tag', Variants::class);

        // Gestion des images
        yield MenuItem::linkToCrud('Images', 'fas fa-image', Pictures::class);

        // Gestion des catégories
        yield MenuItem::linkToCrud('Catégories', 'fas fa-folder', Categories::class);

    }
}
