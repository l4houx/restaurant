<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateEditTrait;
use App\Entity\Company\Client;
use App\Validator\CompanyNumber;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class ClientCrudController extends AbstractCrudController
{
    use CreateEditTrait;

    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Client'));
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
        yield AssociationField::new('member', t('Member'))
            ->autocomplete()
            ->setCrudController(MemberCrudController::class)
        ;
        yield AssociationField::new('salesPerson', t('Sales Person'))
            ->autocomplete()
            ->setCrudController(SalesPersonCrudController::class)
        ;
        yield AssociationField::new('customers', t('Users'))
            ->setTemplatePath('admin/field/client_customers.html.twig')
            ->onlyOnDetail()
        ;
        yield FormField::addPanel(t('Address'));
        yield TextField::new('address.streetAddress', t('Address'))->hideOnIndex();
        yield TextField::new('address.restAddress', t('Additional address'))->hideOnIndex();
        yield TextField::new('address.zipCode', t('Postal code'))->hideOnIndex();
        yield TextField::new('address.locality', t('City'))->hideOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Client'))
            ->setEntityLabelInPlural(t('Clients'))
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }
}
