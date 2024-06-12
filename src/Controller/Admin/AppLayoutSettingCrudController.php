<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateReadDeleteTrait;
use App\Entity\AppLayoutSetting;
use App\Entity\Traits\HasRoles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;

use function Symfony\Component\Translation\t;

class AppLayoutSettingCrudController extends AbstractCrudController
{
    use CreateReadDeleteTrait;

    public static function getEntityFqcn(): string
    {
        return AppLayoutSetting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Logo'));
        yield IdField::new('id')->onlyOnIndex();

        yield ImageField::new('logoName')
            ->setUploadDir('public/uploads/layout/')
            ->setBasePath('/uploads/layout')
            ->hideOnForm()
        ;
        yield TextField::new('logoFile')->setFormType(VichFileType::class)->onlyOnForms();

        yield FormField::addPanel(t('Favicon'));
        yield ImageField::new('faviconName')
            ->setUploadDir('public/uploads/layout/')
            ->setBasePath('/uploads/layout')
            ->hideOnForm()
        ;
        yield TextField::new('faviconFile')->setFormType(VichFileType::class)->onlyOnForms();

        yield FormField::addPanel(t('Og Image'));
        yield ImageField::new('ogImageName')
            ->setUploadDir('public/uploads/layout/')
            ->setBasePath('/uploads/layout')
            ->hideOnForm()
        ;
        yield TextField::new('ogImageFile')->setFormType(VichFileType::class)->onlyOnForms();

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->hideOnIndex();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->hideOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setSearchFields(null)
            ->setEntityLabelInSingular(t('Layout'))
            ->setEntityLabelInPlural(t('Layouts'))
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->showEntityActionsInlined()
        ;
    }
}
