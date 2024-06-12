<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateReadDeleteTrait;
use App\Entity\Currency;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class CurrencyCrudController extends AbstractCrudController
{
    use CreateReadDeleteTrait;

    public static function getEntityFqcn(): string
    {
        return Currency::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('ccy', t('CCY'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 3, max: 3),
            ])
        ;
        yield TextField::new('symbol', t('Symbol'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 1, max: 50),
            ])
        ;
    }
}
