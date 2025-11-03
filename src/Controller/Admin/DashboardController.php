<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Products;
use App\Entity\Variants;
use App\Entity\Pictures;
use App\Entity\Categories;
use App\Entity\Orders;
use App\Controller\Admin\ArchivedOrdersCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Admin dashboard controller for the back-office.
 * Only users with ROLE_ADMIN can access this controller.
 * It sets up the dashboard, default page, and menu items for managing entities.
 */
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        // Redirect to the User CRUD controller by default when accessing /admin
        $url = $adminUrlGenerator->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    // Configure the dashboard title
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Back-Office Boutique');
    }

    // Configure the menu items displayed in the admin panel
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home'); // Link to dashboard home

        // User management
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);

        // Product management
        yield MenuItem::linkToCrud('Produits', 'fas fa-box', Products::class);

        // Variants management (commented out)
        // yield MenuItem::linkToCrud('Variantes', 'fas fa-tag', Variants::class);

        // Pictures management (commented out)
        // yield MenuItem::linkToCrud('Images', 'fas fa-image', Pictures::class);

        // Category management
        yield MenuItem::linkToCrud('Catégories', 'fas fa-folder', Categories::class);

        // Orders management
        yield MenuItem::linkToCrud('Commandes', 'fas fa-folder', Orders::class);

        // Archived orders management using a specific CRUD controller
        yield MenuItem::linkToCrud('Commandes archivées', 'fa fa-archive', Orders::class)
            ->setController(ArchivedOrdersCrudController::class);
        // Links to the Orders entity, but uses the ArchivedOrdersCrudController to handle it
        // EasyAdmin detects that ArchivedOrdersCrudController extends AbstractCrudController
        // and uses its logic, including the createIndexQueryBuilder filter to show only archived orders
    }
}
