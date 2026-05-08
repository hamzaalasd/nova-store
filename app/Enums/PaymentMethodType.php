<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case MANUAL = 'manual';
    case CASH = 'cash';
    case ONLINE = 'online';
}
