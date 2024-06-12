<?php

namespace App\Service;

use App\Entity\User;
use App\Event\SuspendedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

class SuspendedService
{
    public function __construct(private readonly EventDispatcherInterface $dispatcher)
    {
    }

    public function suspended(User $user): void
    {
        $user->setIsSuspended(true);
        $this->dispatcher->dispatch(new SuspendedEvent($user));
    }
}
