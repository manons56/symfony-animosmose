<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCreatorController extends AbstractController
{
    #[Route('/create-admin', name: 'create_admin')]
    public function createAdmin(EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        // Vérifier s'il existe déjà un admin
        $existingAdmin = $em->getRepository(User::class)->findOneBy(['roles' => ['ROLE_ADMIN', 'ROLE_USER']]);
        if ($existingAdmin) {
            return new Response('Admin déjà créé.');
        }

        $admin = new User();
        $admin->setName('AdminName');
        $admin->setSurname('AdminSurname');
        $admin->setPhone('0789898989');
        $admin->setEmail('admin@test.com');
        $admin->setPassword($hasher->hashPassword($admin, 'adminpassword'));
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $em->persist($admin);
        $em->flush();

        return new Response('Admin créé avec succès !');
    }
}
