<?php

declare(strict_types=1);

namespace App\Controller\Admin\Field;

use App\Enum\StatusEnum;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

final class StatusField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): StatusField
    {
        return (new self())
            ->setFormType(EnumType::class)
            ->setFormTypeOption('class', StatusEnum::class)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/field/status.html.twig')
        ;
    }
}
