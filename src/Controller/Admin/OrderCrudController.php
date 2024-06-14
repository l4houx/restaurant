<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\LinesField;
use App\Controller\Admin\Field\OrderStateField;
use App\Controller\Admin\Traits\ReadOnlyTrait;
use App\Entity\Order\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

use function Symfony\Component\Translation\t;

class OrderCrudController extends AbstractCrudController
{
    use ReadOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('ref', t('Refernence'));
        yield AssociationField::new('user', t('Sponsor'))
            ->setCrudController(UserCrudController::class)
        ;
        yield DateTimeField::new('createdAt', t('Order date'));
        yield IntegerField::new('total', t('Total (points)'));
        yield OrderStateField::new('state', 'State');
        yield LinesField::new('lines', 'Command lines')->onlyOnDetail();
        // yield TransactionsField::new("transactions", "Transactions")->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Account'))
            ->setEntityLabelInPlural(t('Accounts'))
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
