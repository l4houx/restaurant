<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait HasReferenceTrait
{
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $ref;

    public function generateReference(int $length): string
    {
        $ref = implode('', [
            bin2hex(random_bytes(2)),
            bin2hex(random_bytes(2)),
            bin2hex(\chr((\ord(random_bytes(1)) & 0x0F) | 0x40)) . bin2hex(random_bytes(1)),
            bin2hex(\chr((\ord(random_bytes(1)) & 0x3F) | 0x80)) . bin2hex(random_bytes(1)),
            bin2hex(random_bytes(2)),
        ]);

        return mb_strlen($ref) > $length ? mb_substr($ref, 0, $length) : $ref;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }
}
