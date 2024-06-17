<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateEditTrait;
use App\Entity\Review;
use App\Entity\Traits\HasRoles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
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
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class ReviewCrudController extends AbstractCrudController
{
    // use CreateEditTrait;

    public static function getEntityFqcn(): string
    {
        return Review::class;
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
                '★★★★★ (5/5)' => 5,
                '★★★★☆ (4/5)' => 4,
                '★★★☆☆ (3/5)' => 3,
                '★★☆☆☆ (2/5)' => 2,
                '★☆☆☆☆ (1/5)' => 1,
            ])
            ->setRequired(Crud::PAGE_NEW === $pageName)
            // ->setRequired(isRequired: true)
            ->autocomplete()
            ->renderAsNativeWidget()
        ;
        // yield AssociationField::new('product', t('Product'))->autocomplete();
        yield AssociationField::new('author', t('Author'))->autocomplete();

        yield FormField::addPanel(t('Actived'));
        yield BooleanField::new('isVisible', t('Published'));

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deletion date'))->hideOnForm()->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Review'))
            ->setEntityLabelInPlural(t('Reviews'))
            ->setDefaultSort(['createdAt' => 'DESC', 'author' => 'ASC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', t('Name')))
            ->add(EntityFilter::new('author', t('Author')))
            ->add(DateTimeFilter::new('createdAt', t('Creation date')))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewReview = Action::new('viewReview', t('See the product review'))
            ->setHtmlAttributes([
                'target' => '_blank',
            ])
            ->linkToCrudAction('viewReview')
        ;

        return $actions
            ->add(Crud::PAGE_EDIT, $viewReview)
            ->add(Crud::PAGE_INDEX, $viewReview)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            // ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ;
    }

    public function viewReview(AdminContext $context): Response
    {
        /** @var Review $entity */
        $entity = $context->getEntity()->getInstance();

        return $this->redirectToRoute('review_show', [
            'slug' => $entity->getSlug(),
        ]);
    }
}
