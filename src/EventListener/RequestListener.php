<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Rules;
use App\Repository\RulesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class RequestListener
{
    /**
     * @param RulesRepository<Rules> $rulesRepository
     */
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RulesRepository $rulesRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function onRequest(RequestEvent $event): void
    {
        $this->em->getFilters()->enable("softdeleteable");

        if (!$this->tokenStorage->getToken()?->getUser() instanceof User) {
            return;
        }

        if ($event->getRequest()->attributes->get("_route") === "rules") {
            return;
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $rules = $this->rulesRepository->getLastPublishedRules();

        if ($user->hasAcceptedRules($rules)) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->urlGenerator->generate("rules")));
    }
}
