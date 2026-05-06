<?php

namespace App\Enums;

enum RaffleStatus: string
{
    case Draft      = 'draft';
    case Active     = 'active';
    case Closed     = 'closed';
    case Generating = 'generating';
}