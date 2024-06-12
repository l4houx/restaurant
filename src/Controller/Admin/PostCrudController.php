<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Traits\HasRoles;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;

use function Symfony\Component\Translation\t;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
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
        yield IntegerField::new('readtime', t('Reading time'))->hideOnIndex();

        yield FormField::addPanel(t('Image'));
        yield ImageField::new('imageName')
            ->setUploadDir('public/uploads/post/')
            ->setBasePath('/uploads/post')
            ->hideOnForm()
        ;
        yield TextField::new('imageFile')->setFormType(VichFileType::class)->onlyOnForms();

        yield FormField::addPanel(t('SEO'));
        yield TextField::new('metaTitle', t('Title'))->hideOnIndex();
        yield TextareaField::new('metaDescription', t('Description'))->renderAsHtml()->hideOnIndex();

        yield FormField::addPanel(t('Actived'));
        yield BooleanField::new('isOnline', t('Published'));

        yield FormField::addPanel(t('Publication date'));
        yield DateTimeField::new('publishedAt', t('Published At'))
            ->setFormTypeOption('constraints', [
                // new NotBlank(),
                new GreaterThan(new \DateTimeImmutable()),
            ])
        ;

        yield FormField::addPanel(t('Association'));
        yield AssociationField::new('type', t('Post Type'))->autocomplete()->hideOnIndex();
        yield AssociationField::new('author', t('Author'))->autocomplete();
        yield AssociationField::new('category', t('Category'))->autocomplete()->hideOnIndex();
        yield AssociationField::new('comments', t('Comments'))->onlyOnDetail();

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deletion date'))->hideOnForm()->onlyOnDetail();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('createdAt')
            ->add('publishedAt')
            ->add('author')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::EDITOR)
            ->setSearchFields(['name'])
            ->setDefaultSort(['publishedAt' => 'DESC', 'name' => 'ASC'])
            ->setAutofocusSearch()
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Post $entity */
        $entity = $entityInstance;

        $entity->setAuthor($this->getUser());

        parent::persistEntity($entityManager, $entity);
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewArticle = Action::new('viewArticle', t('See the article'))
            ->setHtmlAttributes([
                'target' => '_blank',
            ])
            ->linkToCrudAction('viewArticle')
        ;

        return $actions
            ->add(Crud::PAGE_EDIT, $viewArticle)
            ->add(Crud::PAGE_INDEX, $viewArticle)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
        ;
    }

    public function viewArticle(AdminContext $context): Response
    {
        /** @var Post $entity */
        $entity = $context->getEntity()->getInstance();

        return $this->redirectToRoute('post', [
            'slug' => $entity->getSlug(),
        ]);
    }
}
