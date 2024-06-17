<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\DetailOnlyTrait;
use App\Entity\Data\Transaction;
use App\Entity\Traits\HasRoles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

use function Symfony\Component\Translation\t;

class TransactionCrudController extends AbstractCrudController
{
    use DetailOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Transaction::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('reference', t('Reference'))->onlyOnDetail();
        yield AssociationField::new('account', t('Point account'))
            ->setCrudController(AccountCrudController::class)
        ;
        yield AssociationField::new('wallet', t('Wallet'))
            ->setCrudController(WalletCrudController::class);
        yield DateTimeField::new('createdAt', t('Date'));
        yield TextField::new('type', t('Operation'));
        yield IntegerField::new('points', t('Points'));
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Transaction'))
            ->setEntityLabelInPlural(t('Transactions'))
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('account', t('Point account')))
            ->add(DateTimeFilter::new('createdAt', t('Creation date')))
        ;
    }
}
