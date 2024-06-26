<?php

declare(strict_types=1);

namespace App\Controller\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class StatusStateField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): StatusStateField
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath("admin/field/status_state.html.twig")
        ;
    }
}
