<?php

declare(strict_types=1);

namespace App\Controller\Admin\Field;

use App\Form\Type\IconType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

final class IconField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param TranslatableInterface|string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/field/icons.html.twig')
            ->setFormType(IconType::class)
            ->addCssClass('field-text')
            ->setDefaultColumns('col-md-6 col-xxl-5')

            ->addCssFiles(Asset::new('assets/css/fontawesome-iconpicker.css'))
            ->addJsFiles(
                Asset::new('assets/js/jquery.min.js'),
                Asset::new('assets/js/fontawesome-iconpicker.js'),
                Asset::new('assets/js/iconpicker.js')->defer()
            )
        ;
    }
}
