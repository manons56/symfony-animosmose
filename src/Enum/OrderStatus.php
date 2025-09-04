<?php

namespace App\Enum;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Delivered = 'delivered';
    case Canceled = 'canceled';


    public function getLabel(): string
    {
        return match ($this) {
            self::pending => 'En attente de paiement',
            self::paid => 'Payée',
            self::delivered => 'Livrée',
            self::canceled => 'Annulée',
        };
    }
}
