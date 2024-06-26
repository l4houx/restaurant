<?php

namespace App\Twig;

use Symfony\Component\Intl\Locales;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigAppLocaleExtension extends AbstractExtension
{
    /**
     * @var list<array{code: string, name: string}>|null
     */
    private ?array $locales = null;

    public function __construct(
        /** @var string[] */
        private readonly array $enabledLocales,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('locales', $this->getLocales(...)),
        ];
    }

    /**
     * Takes the list of codes of the locales (languages) enabled in the
     * application and returns an array with the name of each locale written
     * in its own language (e.g. English, Français, etc.).
     *
     * @return array<int, array<string, string>>
     */
    public function getLocales(): array
    {
        if (null !== $this->locales) {
            return $this->locales;
        }

        $this->locales = [];

        foreach ($this->enabledLocales as $localeCode) {
            $this->locales[] = ['code' => $localeCode, 'name' => Locales::getName($localeCode, $localeCode)];
        }

        return $this->locales;
    }
}
