<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordUserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function updatePasswordUser(BeforeEntityPersistedEvent|BeforeEntityUpdatedEvent $event): void
    {
        //$entity = $event->getEntityInstance();

        /*if ($entity instanceof User && $entity->getPassword()) {
            $hashedPassword = $this->userPasswordHasher->hashPassword($entity, $entity->getPassword());

            $entity->setPassword($hashedPassword);
        }*/
    }

    /**
     * @codeCoverageIgnore
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'updatePasswordUser',
            BeforeEntityUpdatedEvent::class => 'updatePasswordUser',
        ];
    }
}
