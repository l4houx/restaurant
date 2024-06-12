<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateReadDeleteTrait;
use App\Entity\HelpCenterFaq;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class HelpCenterFaqCrudController extends AbstractCrudController
{
    use CreateReadDeleteTrait;

    public static function getEntityFqcn(): string
    {
        return HelpCenterFaq::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Faq'));
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('question', t('Question title'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
            ])
        ;
        if (Crud::PAGE_NEW === $pageName) {
            yield TextEditorField::new('answer', t('Answer'))
                ->setFormTypeOption('constraints', [
                    new NotBlank(),
                ])
                ->hideOnIndex()
            ;
        } else {
            yield TextareaField::new('answer', t('Answer'))
                ->setFormTypeOption('constraints', [
                    new NotBlank(),
                ])
                ->renderAsHtml()->hideOnIndex()
            ;
        }

        yield IntegerField::new('views', t('Views'))->hideOnForm()->hideOnIndex();

        yield FormField::addPanel(t('SEO'));
        yield TextField::new('metaTitle', t('Title'))->hideOnIndex();
        yield TextareaField::new('metaDescription', t('Description'))->renderAsHtml()->hideOnIndex();

        yield FormField::addPanel(t('Actived'));
        yield BooleanField::new('isOnline', t('Published'));

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deletion date'))->hideOnForm()->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Question'))
            ->setEntityLabelInPlural(t('Questions'))
            ->setDefaultSort(['createdAt' => 'DESC', 'question' => 'ASC'])
        ;
    }
}
