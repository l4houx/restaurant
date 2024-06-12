<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateReadDeleteTrait;
use App\Entity\Page;
use App\Entity\Traits\HasRoles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class PageCrudController extends AbstractCrudController
{
    use CreateReadDeleteTrait;

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('name', t('Name'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(['min' => 4, 'max' => 128]),
            ])
        ;
        yield SlugField::new('slug')
            ->setTargetFieldName('name')
            ->hideOnIndex()
        ;
        yield TextareaField::new('content', t('Content'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(['min' => 2000]),
            ])
            ->renderAsHtml()->hideOnIndex()
        ;
        yield IntegerField::new('views', t('Views'))->hideOnForm();

        yield FormField::addPanel(t('SEO'));
        yield TextField::new('metaTitle', t('Title'))->hideOnIndex();
        yield TextareaField::new('metaDescription', t('Description'))->renderAsHtml()->hideOnIndex();

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Page'))
            ->setEntityLabelInPlural(t('Pages'))
            ->setDefaultSort(['createdAt' => 'DESC', 'name' => 'ASC'])
        ;
    }
}
