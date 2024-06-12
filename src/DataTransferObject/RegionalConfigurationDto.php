<?php

namespace App\DataTransferObject;

final class RegionalConfigurationDto
{
    private ?string $timezone = null;
    private ?string $currency = null;
    private ?string $locale = null;

    public function __construct(
        ?string $timezone = null,
        ?string $currency = null,
        ?string $locale = null
    ) {
        $this->timezone = $timezone;
        $this->currency = $currency;
        $this->locale = $locale;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
