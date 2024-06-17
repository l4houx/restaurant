<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateEditTrait;
use App\Entity\Comment;
use App\Entity\Traits\HasRoles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use function Symfony\Component\Translation\t;

class CommentCrudController extends AbstractCrudController
{
    use CreateEditTrait;

    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();

        yield TextareaField::new('content', t('Content'))->onlyOnDetail();

        yield FormField::addPanel(t('Association'));
        yield AssociationField::new('parent', t('Parent'))->hideOnIndex();
        yield AssociationField::new('post', t('Post'))->autocomplete();
        yield AssociationField::new('author', t('Author'))->autocomplete();

        yield FormField::addPanel(t('Actived'));
        yield BooleanField::new('isApproved', t('Approved'));
        yield BooleanField::new('isRGPD', t('RGPD'))->hideOnForm()->onlyOnDetail();

        yield FormField::addPanel(t('Date'));
        yield DateTimeField::new('publishedAt', t('Published date'))->onlyOnDetail();

        yield FormField::addPanel(t('IP adress'));
        yield TextField::new('ip', t('IP'))->hideOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Comment'))
            ->setEntityLabelInPlural(t('Comments'))
            ->setSearchFields(['author', 'email', 'content'])
            ->setDefaultSort(['publishedAt' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('publishedAt')
            ->add('post')
            ->add('author')
            ->add('isApproved')
        ;
    }
}
