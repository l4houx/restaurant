<?php

namespace App\EventSubscriber;

use App\Data\TransferPointsInterface;
use App\Entity\Data\Transfer;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TransferSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TransferPointsInterface $transferPoint)
    {
    }

    public function prePersist(BeforeEntityPersistedEvent $event): void
    {
        if (!$event->getEntityInstance() instanceof Transfer) {
            return;
        }

        /** @var Transfer $transfer */
        $transfer = $event->getEntityInstance();

        $this->transferPoint->execute($transfer);
    }

    /**
     * @codeCoverageIgnore
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [BeforeEntityPersistedEvent::class => ['prePersist']];
    }
}
