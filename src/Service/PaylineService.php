<?php

namespace App\Service;

use Payline\PaylineSDK;

class PaylineService
{
    // Objet PaylineSDK pour communiquer avec l'API Payline
    private PaylineSDK $payline;

    // Numéro de contrat marchand Payline
    private string $contractNumber;

    public function __construct(string $merchantId, string $accessKey, string $contractNumber, string $environment)
    {
        // On stocke le numéro de contrat
        $this->contractNumber = $contractNumber;

        // On initialise le SDK Payline avec les informations nécessaires
        $this->payline = new PaylineSDK(
            $merchantId,                              // Identifiant marchand
            $accessKey,                               // Clé secrète
            $contractNumber,                          // Numéro de contrat
            $environment, // Choix entre l'environnement TEST ou PRODUCTION
            'https://webpayment.payline.com/webpayment/getToken', // URL de l'endpoint de génération de token
            '1.0',                                    // Version de l’API
            'Payline SDK PHP v4.77'                   // Description du SDK utilisé
        );
    }

    /**
     * Crée une session de paiement (redirection utilisateur vers Payline)
     *
     * @param float  $amount    Montant du paiement en euros
     * @param string $returnUrl URL de retour en cas de succès
     * @param string $cancelUrl URL de retour en cas d’annulation
     * @param string $orderRef  Référence unique de la commande
     *
     * @return array Réponse de l’API Payline (incluant notamment le token et l’URL de paiement)
     */
    public function createPaymentSession(float $amount, string $returnUrl, string $cancelUrl, string $orderRef): array
    {
        // Paramètres de la transaction envoyés à Payline
        $params = [
            'payment' => [
                'amount' => intval($amount * 100), // Montant en centimes (Payline ne gère pas les décimales)
                'currency' => 978,                 // Devise : 978 = Euro (ISO 4217)
                'action' => 101,                   // Action 101 = "PAYMENT" (paiement immédiat)
                'mode' => 'CPT',                   // Mode CPT = "Comptant" (paiement en une seule fois)
            ],
            'order' => [
                'ref' => $orderRef,                // Référence unique de la commande
                'amount' => intval($amount * 100), // Montant total en centimes
                'currency' => 978,                 // Devise Euro
                'date' => date('d/m/Y H:i'),       // Date de la commande
            ],
            'contracts' => [$this->contractNumber], // Liste des contrats à utiliser (ici 1 contrat)
            'returnURL' => $returnUrl,              // URL appelée si paiement validé
            'cancelURL' => $cancelUrl,              // URL appelée si paiement annulé
        ];

        // Appel au SDK Payline pour créer la session de paiement
        return $this->payline->doWebPayment($params);
    }

    /**
     * Récupère les détails d’un paiement à partir du token
     *
     * @param string $token Token de la transaction fourni par Payline
     *
     * @return array Détails du paiement (statut, infos carte, etc.)
     */
    public function getPaymentDetails(string $token): array
    {
        // On appelle le SDK Payline pour récupérer les infos de la transaction
        return $this->payline->getWebPaymentDetails(['token' => $token]);
    }
}
