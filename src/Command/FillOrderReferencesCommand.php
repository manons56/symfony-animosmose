<?php

namespace App\Command;

use App\Entity\Orders;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * FillOrderReferencesCommand
 * --------------------------
 * This Symfony console command assigns a unique reference to each order
 * that is missing one and saves the changes in the database.
 *
 * Features:
 * 1. Fetches all orders from the database.
 * 2. Checks if each order has a reference.
 * 3. Generates a unique reference (prefixed with "CMD-") if missing.
 * 4. Persists the changes to the database.
 * 5. Outputs progress messages in the console.
 */
class FillOrderReferencesCommand extends Command
{
    // Doctrine Entity Manager for interacting with the database
    private EntityManagerInterface $em;

    /**
     * Constructor: injects the EntityManager to handle database operations
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(); // Call parent Command constructor
        $this->em = $em;       // Store EntityManager for later use
    }

    /**
     * Configure the Symfony command
     */
    protected function configure(): void
    {
        $this
            ->setName('app:fill-order-references') // CLI command name
            ->setDescription('Fills missing references for all existing orders.'); // Description for console help
    }

    /**
     * Execute the command
     *
     * @param InputInterface  $input  Input parameters (not used here)
     * @param OutputInterface $output Console output interface
     *
     * @return int Returns SUCCESS (0) if executed successfully
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Retrieve all orders from the database
        $orders = $this->em->getRepository(Orders::class)->findAll();

        // Loop through each order
        foreach ($orders as $order) {
            // If the order has no reference
            if (!$order->getReference()) {
                // Generate a unique reference with prefix "CMD-"
                $order->setReference(uniqid('CMD-'));

                // Tell Doctrine to persist this entity
                $this->em->persist($order);

                // Output a message to the console
                $output->writeln("Reference added for order ID " . $order->getId());
            }
        }

        // Flush all changes to the database
        $this->em->flush();

        // Output a final message when done
        $output->writeln("All missing references filled.");

        // Return success code for Symfony
        return Command::SUCCESS;
    }
}
