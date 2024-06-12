<?php

namespace App\DataTransferObject;

use App\Entity\Order\Line;

class SavFormDTO
{
    public Line $line;

    public string $description = '';

    public ?string $comment = null;

    /**
     * @var array<int, string>
     */
    public array $attachments = [];
}
