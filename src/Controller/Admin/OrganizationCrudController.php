<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateEditTrait;
use App\Entity\Company\Organization;
use App\Validator\CompanyNumber;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class OrganizationCrudController extends AbstractCrudController
{
    use CreateEditTrait;

    public static function getEntityFqcn(): string
    {
        return Organization::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Group'));
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('type', t('Typology'))
            ->hideOnForm()
        ;
        yield TextField::new('name', t('Social reason'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
            ])
        ;
        yield TextField::new('companyNumber', t('SIRET No'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new CompanyNumber(),
            ])
        ;
        yield TextField::new('vatNumber', t('Internal VAT No.'))
            ->hideOnForm()
        ;
        yield AssociationField::new('members', t('Member'))
            ->setCrudController(MemberCrudController::class)
            ->setTemplatePath('admin/field/organization_members.html.twig')
            ->onlyOnDetail()
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Group'))
            ->setEntityLabelInPlural(t('Groups'))
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }
}
