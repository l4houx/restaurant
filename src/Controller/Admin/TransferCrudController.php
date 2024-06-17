<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\TransactionsField;
use App\Controller\Admin\Traits\DetailOnlyTrait;
use App\Entity\Data\Account;
use App\Entity\Data\Transfer;
use App\Entity\Traits\HasRoles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

use function Symfony\Component\Translation\t;

class TransferCrudController extends AbstractCrudController
{
    use DetailOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Transfer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('from', t('Issuer'))
            ->setFormTypeOption('choice_label', fn (Account $account) => sprintf(
                t('%s - Balance : %d keys'),
                $account->__toString(),
                $account->getBalance()
            ))
            ->setCrudController(AccountCrudController::class)
        ;
        yield AssociationField::new('to', t('Recipient'))
            ->setFormTypeOption('choice_label', fn (Account $account) => sprintf(
                t('%s - Balance : %d keys'),
                $account->__toString(),
                $account->getBalance()
            ))
            ->setCrudController(AccountCrudController::class)
        ;
        yield DateTimeField::new('createdAt', t('Date'))->hideOnForm();
        yield IntegerField::new('points', t('Points'));
        yield TransactionsField::new('transactions', t('Transactions'))->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Transfer'))
            ->setEntityLabelInPlural(t('Transfers'))
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('from', t('Issuer')))
            ->add(EntityFilter::new('to', t('Recipient')))
            ->add(DateTimeFilter::new('createdAt', t('Date')))
        ;
    }
}
