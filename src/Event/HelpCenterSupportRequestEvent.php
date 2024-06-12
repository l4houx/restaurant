<?php

namespace App\Event;

use App\DataTransferObject\HelpCenterSupportDTO;
use Symfony\Contracts\EventDispatcher\Event;

class HelpCenterSupportRequestEvent extends Event
{
    public function __construct(
        public readonly HelpCenterSupportDTO $data
    ) {
    }
}