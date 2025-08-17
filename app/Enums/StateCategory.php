<?php

namespace App\Enums;

enum StateCategory: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case FINALIZED = 'finalized';

    case CANCELLED = 'cancelled';

}
