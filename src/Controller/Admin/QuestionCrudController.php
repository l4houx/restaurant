<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateReadDeleteTrait;
use App\Entity\Question;
use App\Entity\Traits\HasRoles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class QuestionCrudController extends AbstractCrudController
{
    use CreateReadDeleteTrait;

    public static function getEntityFqcn(): string
    {
        return Question::class;
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

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Question'))
            ->setEntityLabelInPlural(t('Questions'))
            ->setDefaultSort(['question' => 'ASC'])
        ;
    }
}
