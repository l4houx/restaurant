<?php

declare(strict_types=1);

namespace App\Enum;

enum StatusEnum: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Rejected = 'rejected';
}
