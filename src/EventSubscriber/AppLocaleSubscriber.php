<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function Symfony\Component\String\u;

class AppLocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        /** @var string[] */
        private array $enabledLocales,
        private ?string $defaultLocale = null
    ) {
        if (empty($this->enabledLocales)) {
            throw new \UnexpectedValueException('The list of supported locales must not be empty.');
        }

        $this->defaultLocale = $defaultLocale ?: $this->enabledLocales[0];

        if (!\in_array($this->defaultLocale, $this->enabledLocales, true)) {
            throw new \UnexpectedValueException(sprintf('The default locale ("%s") must be one of "%s".', $this->defaultLocale, implode(', ', $this->enabledLocales)));
        }

        array_unshift($this->enabledLocales, $this->defaultLocale);
        $this->enabledLocales = array_unique($this->enabledLocales);
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMainRequest() || '/' !== $request->getPathInfo()) {
            return;
        }

        $referrer = $request->headers->get('referer');
        if (null !== $referrer && u($referrer)->ignoreCase()->startsWith($request->getSchemeAndHttpHost())) {
            return;
        }

        $preferredLanguage = $request->getPreferredLanguage($this->enabledLocales);

        if ($preferredLanguage !== $this->defaultLocale) {
            $response = new RedirectResponse($this->urlGenerator->generate('home', ['_locale' => $preferredLanguage]));
            $event->setResponse($response);
        }
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
