<?php

declare(strict_types=1);

namespace App\Service;

use Jdenticon\Identicon;

final class AvatarService
{
    public function createAvatar(string $hashString): string
    {
        $icon = new Identicon();

        $icon->setSize(300);
        $icon->setHash($hashString);
        $icon->setValue($hashString);

        return $icon->getImageDataUri();
    }
}
