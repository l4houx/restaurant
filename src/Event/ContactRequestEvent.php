<?php

namespace App\Event;

use App\DataTransferObject\ContactFormDTO;
use Symfony\Contracts\EventDispatcher\Event;

class ContactRequestEvent extends Event
{
    public function __construct(
        public readonly ContactFormDTO $data
    ) {
    }
}
