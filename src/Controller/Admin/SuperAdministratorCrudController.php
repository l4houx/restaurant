<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateEditTrait;
use App\Entity\Traits\HasRoles;
use App\Entity\User\SuperAdministrator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use function Symfony\Component\Translation\t;

class SuperAdministratorCrudController extends AbstractCrudController
{
    use CreateEditTrait;

    public static function getEntityFqcn(): string
    {
        return SuperAdministrator::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Administrator'))
            ->setEntityLabelInPlural(t('Administrators'))
            ->setDefaultSort(['firstname' => 'ASC', 'lastname' => 'ASC'])
        ;
    }
}
