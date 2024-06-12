<?php

namespace App\Controller\Admin;

use App\Entity\Traits\HasRoles;
use App\Entity\HelpCenterArticle;
use function Symfony\Component\Translation\t;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Controller\Admin\Traits\CreateReadDeleteTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HelpCenterArticleCrudController extends AbstractCrudController
{
    use CreateReadDeleteTrait;

    public static function getEntityFqcn(): string
    {
        return HelpCenterArticle::class;
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
        yield TextField::new('tags', t('Tags'))
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'class' => 'tags-input',
            ])
            ->setHelp('')
        ;
        if (Crud::PAGE_NEW === $pageName) {
            yield TextEditorField::new('content', t('Content'))
                ->setFormTypeOption('constraints', [
                    new NotBlank(),
                    new Length(['min' => 2000]),
                ])
                ->hideOnIndex()
            ;
        } else {
            yield TextareaField::new('content', t('Content'))
                ->setFormTypeOption('constraints', [
                    new NotBlank(),
                    new Length(['min' => 2000]),
                ])
                ->renderAsHtml()->hideOnIndex()
            ;
        }

        yield IntegerField::new('views', t('Views'))->hideOnForm()->hideOnIndex();
        yield IntegerField::new('featuredorder', t('Featured order'))->hideOnForm()->hideOnIndex();

        yield FormField::addPanel(t('SEO'));
        yield TextField::new('metaTitle', t('Title'))->hideOnIndex();
        yield TextareaField::new('metaDescription', t('Description'))->renderAsHtml()->hideOnIndex();

        yield FormField::addPanel(t('Association'));
        yield AssociationField::new('category', t('Category'))->autocomplete();

        yield FormField::addPanel(t('Actived'));
        yield BooleanField::new('isOnline', t('Published'));
        yield BooleanField::new('isFeatured', t('Featured'));

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deletion date'))->hideOnForm()->onlyOnDetail();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('name')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setSearchFields(['name'])
            ->setDefaultSort(['createdAt' => 'DESC', 'name' => 'ASC'])
            ->setAutofocusSearch()
        ;
    }
}
