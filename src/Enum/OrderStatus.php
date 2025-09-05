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
            self::Pending => 'En attente de paiement',
            self::Paid => 'Payée',
            self::Delivered => 'Livrée',
            self::Canceled => 'Annulée',
        };
    }
}
