<?php

namespace App\EventSubscriber;

use App\Entity\Data\Purchase;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSubscriber implements EventSubscriberInterface
{
    public function prePersist(BeforeEntityPersistedEvent $event): void
    {
        if (!$event->getEntityInstance() instanceof Purchase) {
            return;
        }

        /** @var Purchase $purchase */
        $purchase = $event->getEntityInstance();

        $purchase->prepare();
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
