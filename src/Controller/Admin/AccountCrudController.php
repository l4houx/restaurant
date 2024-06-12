<?php

namespace App\Controller\Admin;

use App\Entity\Data\Account;
use function Symfony\Component\Translation\t;
use App\Controller\Admin\Traits\ReadOnlyTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AccountCrudController extends AbstractCrudController
{
    use ReadOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(DateTimeFilter::new('createdAt', t('Creation date')));
    }
}
