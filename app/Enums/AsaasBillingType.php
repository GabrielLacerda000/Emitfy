<?php

namespace App\Enums;

enum AsaasBillingType: string
{
    case Pix = 'PIX';
    case CreditCard = 'CREDIT_CARD';
    case Boleto = 'BOLETO';
    case Undefined = 'UNDEFINED';
}
