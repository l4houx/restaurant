<?php

declare(strict_types=1);

namespace App\Controller\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class LevelStateField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): LevelStateField
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath("admin/field/level_state.html.twig")
        ;
    }
}
