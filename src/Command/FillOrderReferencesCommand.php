<?php

namespace App\Command;

use App\Entity\Orders;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

 //fichier permettant d'attribuer une réference à chaque commande et l'enregistrer en bdd

class FillOrderReferencesCommand extends Command
{
    // Gestionnaire d'entités Doctrine (permet d’interagir avec la base de données)
    private EntityManagerInterface $em;

    /**
     * Constructeur : on injecte EntityManager pour manipuler les entités
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(); // Appel du constructeur parent de Command
        $this->em = $em;       // On garde EntityManager dans une propriété pour l’utiliser après
    }

    /**
     * Configuration de la commande Symfony
     */
    protected function configure(): void
    {
        $this
            ->setName('app:fill-order-references') // Nom de la commande (celle qu’on exécute en CLI)
            ->setDescription('Remplit les références manquantes pour toutes les commandes existantes.'); // Description affichée
    }

    /**
     * Logique de la commande
     *
     * @param InputInterface  $input  (ici non utilisé)
     * @param OutputInterface $output Pour écrire des messages dans la console
     *
     * @return int Code de retour (SUCCESS si tout s’est bien passé)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // On récupère toutes les commandes en base
        $orders = $this->em->getRepository(Orders::class)->findAll();

        // On boucle sur chaque commande
        foreach ($orders as $order) {
            // Si la commande n’a pas encore de référence
            if (!$order->getReference()) {
                // On génère une référence unique avec un préfixe "CMD-"
                $order->setReference(uniqid('CMD-'));

                // On dit à Doctrine de préparer la sauvegarde de cette entité
                $this->em->persist($order);

                // On affiche un message dans la console pour informer l’utilisateur
                $output->writeln("Reference added for order ID " . $order->getId());
            }
        }

        // On envoie toutes les modifications en base de données
        $this->em->flush();

        // Message final une fois le traitement terminé
        $output->writeln("All missing references filled.");

        // On retourne un code de succès (0) pour Symfony
        return Command::SUCCESS;
    }
}
