<?php

namespace App\Controller\Admin;

use App\Entity\Traits\HasRoles;
use App\Entity\Tickets\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Admin\Traits\CreateTrait;
use function Symfony\Component\Translation\t;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use Symfony\Component\Validator\Constraints\NotBlank;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ResponseCrudController extends AbstractCrudController
{
    use CreateTrait;

    public static function getEntityFqcn(): string
    {
        return Response::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();

        yield TextareaField::new('content', t('Content'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(['min' => 100]),
            ])->hideOnIndex()
        ;

        yield AssociationField::new('ticket', t('Ticket'))->autocomplete();
        yield AssociationField::new('user', t('User'))->autocomplete()->hideOnForm();

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deletion date'))->hideOnForm()->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Response'))
            ->setEntityLabelInPlural(t('Responses'))
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('ticket', t('Ticket')))
            ->add(EntityFilter::new('user', t('User')))
            ->add(DateTimeFilter::new('createdAt', t('Creation date')))
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Response $entity */
        $entity = $entityInstance;

        $entity->setUser($this->getUser());

        parent::persistEntity($entityManager, $entity);
    }
}
