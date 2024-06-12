<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateEditTrait;
use App\Entity\Company\Member;
use App\Validator\CompanyNumber;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class MemberCrudController extends AbstractCrudController
{
    use CreateEditTrait;

    public static function getEntityFqcn(): string
    {
        return Member::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Member'));
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

        yield AssociationField::new('organization', t('Group'))
            ->setCrudController(OrganizationCrudController::class)
        ;

        yield FormField::addPanel(t('Address'));
        yield TextField::new('address.streetAddress', t('Address'))->hideOnIndex();
        yield TextField::new('address.restAddress', t('Additional address'))->hideOnIndex();
        yield TextField::new('address.zipCode', t('Postal code'))->hideOnIndex();
        yield TextField::new('address.locality', t('City'))->hideOnIndex();
        yield EmailField::new('address.email', t('E-mail'))->hideOnIndex();
        yield TextField::new('address.phone', t('Phone'))->hideOnIndex();

        yield FormField::addPanel(t('Access'));
        yield AssociationField::new('clients', t('Clients'))
            ->setTemplatePath('admin/field/member_clients.html.twig')
            ->setCrudController(ClientCrudController::class)
            ->onlyOnDetail()
        ;
        yield AssociationField::new('managers', t('Administrators'))
            ->setTemplatePath('admin/field/member_managers.html.twig')
            ->onlyOnDetail()
        ;
        yield AssociationField::new('salesPersons', t('Commercial'))
            ->setTemplatePath('admin/field/member_sales_persons.html.twig')
            ->setCrudController(SalesPersonCrudController::class)
            ->onlyOnDetail()
        ;
        yield AssociationField::new('collaborators', t('Collaborators'))
            ->setTemplatePath('admin/field/member_collaborators.html.twig')
            ->onlyOnDetail()
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Member'))
            ->setEntityLabelInPlural(t('Members'))
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }
}
