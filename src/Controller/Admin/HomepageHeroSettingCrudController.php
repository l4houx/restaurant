<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateReadDeleteTrait;
use App\Entity\HomepageHeroSetting;
use App\Entity\Traits\HasRoles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Length;
use Vich\UploaderBundle\Form\Type\VichFileType;

use function Symfony\Component\Translation\t;

class HomepageHeroSettingCrudController extends AbstractCrudController
{
    use CreateReadDeleteTrait;

    public static function getEntityFqcn(): string
    {
        return HomepageHeroSetting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('title', t('Title'))
            ->setFormTypeOption('constraints', [
                new Length(['max' => 100]),
            ])
        ;
        yield TextareaField::new('paragraph', t('Paragraph'))->renderAsHtml()->hideOnIndex();
        yield TextareaField::new('content', t('Content'))->renderAsHtml()->hideOnIndex();

        yield FormField::addPanel(t('Custom Background'));
        yield ImageField::new('customBackgroundName')
            ->setUploadDir('public/uploads/home/')
            ->setBasePath('/uploads/home')
            ->hideOnForm()
        ;
        yield TextField::new('customBackgroundFile')->setFormType(VichFileType::class)->onlyOnForms();

        yield FormField::addPanel(t('Custom Block'));
        yield ImageField::new('customBlockOneName')
            ->setUploadDir('public/uploads/home/block/')
            ->setBasePath('/uploads/home/block')
            ->hideOnForm()
        ;
        yield TextField::new('customBlockOneFile')->setFormType(VichFileType::class)->onlyOnForms();

        yield ImageField::new('customBlockTwoName')
            ->setUploadDir('public/uploads/home/block/')
            ->setBasePath('/uploads/home/block')
            ->hideOnForm()
        ;
        yield TextField::new('customBlockTwoFile')->setFormType(VichFileType::class)->onlyOnForms();

        yield ImageField::new('customBlockThreeName')
            ->setUploadDir('public/uploads/home/block/')
            ->setBasePath('/uploads/home/block')
            ->hideOnForm()
        ;
        yield TextField::new('customBlockThreeFile')->setFormType(VichFileType::class)->onlyOnForms();

        // yield AssociationField::new('products', t('Products'))->onlyOnIndex();
        // yield ArrayField::new('products', t('Products'))->onlyOnDetail();
        // yield AssociationField::new('users', t('Users'))->autocomplete();

        yield FormField::addPanel(t('Show Search'));
        yield BooleanField::new('show_search_box', t('Search Box'));

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->hideOnIndex();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->hideOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setSearchFields(null)
            ->setEntityLabelInSingular(t('Hero'))
            ->setEntityLabelInPlural(t('Heros'))
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->showEntityActionsInlined()
        ;
    }
}
