<?php

namespace App\Enums;

enum ProofType: string
{
    case Image             = 'image';
    case TransactionNumber = 'transaction_number';
}