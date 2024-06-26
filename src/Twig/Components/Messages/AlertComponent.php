<?php

declare(strict_types=1);

namespace App\Twig\Components\Messages;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('alert', template: 'components/messages/alert.html.twig')]
final class AlertComponent
{
    public string $type;

    public bool $closeButton = true;

    public function getIcon(): string
    {
        return match ($this->type) {
            'success' => 'circle-check',
            'error' => 'circle-cross',
            'warning' => 'circle-exclamation',
            default => 'circle-info',
        };
    }
}
