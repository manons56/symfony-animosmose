<?php
namespace OnlinePayments\Sdk\Merchant;

use OnlinePayments\Sdk\Communicator;
use OnlinePayments\Sdk\ApiResource;

class MerchantClientFactory
{
    public function createClient(Communicator $communicator, string $merchantId): MerchantClient
    {
        $context = ['merchantId' => $merchantId];

        // Parent factice pour fournir le communicator aux ressources filles
        $parent = new class($communicator) extends ApiResource {
            private Communicator $communicator;

            public function __construct(Communicator $communicator)
            {
                $this->communicator = $communicator;
            }

            protected function getCommunicator(): Communicator
            {
                return $this->communicator;
            }

            protected function getClientMetaInfo(): string
            {
                return ''; // ou mettre des infos sur ton intégrateur si nécessaire
            }
        };

        return new MerchantClient($parent, $context);
    }
}
