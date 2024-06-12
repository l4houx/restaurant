<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateReadDeleteTrait;
use App\Entity\Testimonial;
use App\Entity\Traits\HasRoles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class TestimonialCrudController extends AbstractCrudController
{
    use CreateReadDeleteTrait;

    public static function getEntityFqcn(): string
    {
        return Testimonial::class;
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
        yield TextareaField::new('content', t('Content'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(['min' => 2000]),
            ])->hideOnIndex()
        ;
        yield ChoiceField::new('rating', t('Your rating (out of 5 stars) '))
            ->allowMultipleChoices(false)
            ->renderExpanded(true)
            ->renderAsBadges()
            ->setChoices([
                '5 stars' => 5,
                '4 stars' => 4,
                '3 stars' => 3,
                '2 stars' => 2,
                '1 star' => 1,
            ])
            ->setRequired(isRequired: true)
            ->autocomplete()
            ->renderAsNativeWidget()
        ;
        yield AssociationField::new('author', t('Author'))->autocomplete();

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
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Testimonial'))
            ->setEntityLabelInPlural(t('Testimonials'))
            ->setDefaultSort(['createdAt' => 'DESC', 'author' => 'ASC'])
        ;
    }
}
