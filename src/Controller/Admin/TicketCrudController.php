<?php

namespace App\Controller\Admin;

use App\Entity\Tickets\Ticket;
use App\Entity\Traits\HasRoles;
use function Symfony\Component\Translation\t;
use App\Controller\Admin\Traits\CreateEditTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use Symfony\Component\Validator\Constraints\NotBlank;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TicketCrudController extends AbstractCrudController
{
    use CreateEditTrait;

    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('subject', t('Subject'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 4, max: 128),
            ])
        ;
        yield TextareaField::new('content', t('Content'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(['min' => 100]),
            ])->hideOnIndex()
        ;

        yield AssociationField::new('user', t('User'))->autocomplete()->hideOnForm();
        yield AssociationField::new('status', t('Status'))
            ->setTemplatePath("admin/field/status_state.html.twig")
            ->setCrudController(StatusCrudController::class)
            ->hideOnForm()
        ;
        yield AssociationField::new('level', t('Level'))
            ->setTemplatePath("admin/field/level_state.html.twig")
            ->setCrudController(LevelCrudController::class)
        ;
        yield AssociationField::new('responses', t('Response'))->hideOnForm();

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deletion date'))->hideOnForm()->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Ticket'))
            ->setEntityLabelInPlural(t('Tickets'))
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('subject', t('Subject')))
            ->add(EntityFilter::new('user', t('User')))
            ->add(DateTimeFilter::new('createdAt', t('Creation date')))
        ;
    }
}
