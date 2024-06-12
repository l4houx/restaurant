<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\RolesField;
use App\Controller\Admin\Traits\CreateEditTrait;
use App\Entity\Traits\HasRoles;
use App\Entity\User\SalesPerson;
use App\Service\AvatarService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

use function Symfony\Component\Translation\t;

class SalesPersonCrudController extends AbstractCrudController
{
    use CreateEditTrait;

    public function __construct(private readonly AvatarService $avatarService)
    {
    }

    public static function getEntityFqcn(): string
    {
        return SalesPerson::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Avatar'))->hideOnForm();
        yield TextField::new('avatar')->hideOnForm();

        yield FormField::addPanel(t('Sales Person'));
        yield TextField::new('firstname', t('Firstname'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 2, max: 20),
            ])
        ;
        yield TextField::new('lastname', t('Lastname'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 2, max: 20),
            ])
        ;
        yield TextField::new('username', t('Username'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 4, max: 30),
            ])
        ;
        yield TextField::new('password', t('Password'))
            ->setFormType(PasswordType::class)
            ->setFormTypeOption('constraints', [
                new NotBlank(),
            ])
            ->onlyWhenCreating()
        ;
        yield EmailField::new('email', t('Email'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new NotNull(),
                new Email(),
                new Length(min: 5, max: 180),
            ])
        ;
        yield TextField::new('phone', t('Phone Number'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Regex(['pattern' => '/^0[0-9]{9}$/']),
            ])->hideOnIndex()
        ;

        yield RolesField::new('role', 'Role')
            ->hideOnForm()
        ;

        /*
        yield ChoiceField::new('roles', t('Roles'))
            ->renderExpanded()
            ->renderAsBadges([
                HasRoles::TEAM => 'primary',
            ])
            ->setChoices([
                'Team' => HasRoles::TEAM
            ])
            ->allowMultipleChoices()
            ->setRequired(isRequired: false)
        ;
        */

        yield FormField::addPanel(t('Team'));
        yield BooleanField::new('isTeam', t('Team'))->hideOnIndex();
        yield TextareaField::new('about', t('About'))->hideOnIndex();
        yield TextareaField::new('designation', t('Designation'))->hideOnIndex();

        yield FormField::addPanel(t('Details'));
        yield BooleanField::new('suspended', t('Suspended'))->hideOnIndex();
        yield DateTimeField::new('bannedAt', t('Banner'))->hideOnForm();
        yield DateTimeField::new('lastLogin', t('Last connection'))->hideOnForm();
        yield TextField::new('lastLoginIp', t('IP'))->hideOnForm();

        yield AssociationField::new('member', t('Member'))
            ->setCrudController(MemberCrudController::class)
        ;
        yield AssociationField::new('clients', t('Clients'))
            ->setCrudController(ClientCrudController::class)
            ->setTemplatePath('admin/field/sales_person_clients.html.twig')
            ->onlyOnDetail()
        ;

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deleted At'))->hideOnForm()->hideOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Sales person'))
            ->setEntityLabelInPlural(t('Sales persons'))
            ->setDefaultSort(['firstname' => 'ASC', 'lastname' => 'ASC'])
        ;
    }
}
