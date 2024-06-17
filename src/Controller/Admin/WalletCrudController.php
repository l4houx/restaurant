<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\TransactionsField;
use App\Controller\Admin\Field\WalletStateField;
use App\Controller\Admin\Traits\DetailOnlyTrait;
use App\Entity\Data\Wallet;
use App\Entity\Traits\HasRoles;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

use function Symfony\Component\Translation\t;

class WalletCrudController extends AbstractCrudController
{
    use DetailOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Wallet::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('reference', t('Reference'))->hideOnForm();
        yield AssociationField::new('account', t('Point account'))
            ->setCrudController(AccountCrudController::class)
        ;
        yield AssociationField::new('purchase', t('Initial transaction'))
            ->setCrudController(TransactionCrudController::class)
        ;
        yield DateTimeField::new('createdAt', t('Creation date'));
        yield DateTimeField::new('expiredAt', t('Expiration date'));
        yield IntegerField::new('balance', t('Pay'));
        yield WalletStateField::new('expired', t('Status'))->hideOnForm();
        yield TransactionsField::new('transactions', t('Transactions'))->onlyOnDetail();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('account', t('Point account')))
            ->add(DateTimeFilter::new('createdAt', t('Creation date')))
            ->add(DateTimeFilter::new('expiredAt', t('Expiration date')))
        ;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->addSelect('purchase')
            ->addSelect('account')
            ->join('entity.purchase', 'purchase')
            ->join('entity.account', 'account')
            ->where("purchase.state = 'accepted'")
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Wallet'))
            ->setEntityLabelInPlural(t('Wallets'))
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm:ss')
        ;
    }
}
