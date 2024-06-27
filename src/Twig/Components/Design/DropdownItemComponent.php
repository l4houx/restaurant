<?php

namespace App\Twig\Components\Design;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('dropdown_item', template: 'components/design/dropdown_item.html.twig')]
final class DropdownItemComponent
{
    private ?Request $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getMainRequest();
    }

    public string $label;

    public ?string $route = null;

    public ?string $icon = null;

    public function isActive(): bool
    {
        return null !== $this->request && $this->request->attributes->get('_route') === $this->route;
    }
}
