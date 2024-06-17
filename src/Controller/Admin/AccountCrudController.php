<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\OwnerField;
use App\Controller\Admin\Field\TransactionsField;
use App\Controller\Admin\Field\WalletsField;
use App\Controller\Admin\Traits\DetailOnlyTrait;
use App\Entity\Data\Account;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

use function Symfony\Component\Translation\t;

class AccountCrudController extends AbstractCrudController
{
    use DetailOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield OwnerField::new('owner', t('Owner'));
        yield DateTimeField::new('createdAt', 'Creation date');
        yield IntegerField::new('balance', t('Points balance'));
        yield WalletsField::new('wallets', t('Wallets'))->onlyOnDetail();
        yield TransactionsField::new('transactions', t('Transactions'))->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Points account'))
            ->setEntityLabelInPlural(t('Points accounts'))
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(DateTimeFilter::new('createdAt', t('Creation date')));
    }
}
