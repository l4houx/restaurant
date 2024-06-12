<?php

namespace App\Controller\Admin;

use App\Entity\Traits\HasRoles;
use App\Entity\HelpCenterCategory;
use App\Controller\Admin\Field\IconField;
use function Symfony\Component\Translation\t;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Controller\Admin\Traits\CreateReadDeleteTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HelpCenterCategoryCrudController extends AbstractCrudController
{
    use CreateReadDeleteTrait;

    public static function getEntityFqcn(): string
    {
        return HelpCenterCategory::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('name', t('Name'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 4, max: 128),
            ])
        ;
        yield SlugField::new('slug')
            ->setTargetFieldName('name')
            ->hideOnIndex()
            ->hideOnForm()
        ;
        yield IconField::new('icon', t('Icon'));
        yield ColorField::new('color', t('Color'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 4, max: 12),
            ])
        ;

        yield FormField::addPanel(t('Actived'));
        yield BooleanField::new('isOnline', t('Published'));

        yield FormField::addPanel(t('Association'));
        yield AssociationField::new('articles', t('Articles'))
            ->autocomplete()
            ->setFormTypeOption('by_reference', false)
        ;
        //yield AssociationField::new('articles', t('Articles'))->autocomplete()->onlyOnIndex();
        //yield ArrayField::new('articles', t('Articles'))->onlyOnDetail();

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deletion date'))->hideOnForm()->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Category'))
            ->setEntityLabelInPlural(t('Categories'))
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }
}
