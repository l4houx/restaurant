<?php

namespace App\Controller\Admin;

use App\Entity\Shop\Product;
use function Symfony\Component\Translation\t;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Product'))
            ->setEntityLabelInPlural(t('Products'))
            ->setDefaultSort(['createdAt' => 'DESC', 'name' => 'ASC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm:ss')
            ->setSearchFields(['name'])
            ->setAutofocusSearch()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', t('Name')))
            ->add(EntityFilter::new('category', t('Category')))
            ->add(DateTimeFilter::new('createdAt', t('Creation date')))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewProduct = Action::new('viewProduct', t("See the product"))
            ->setHtmlAttributes([
                'target' => '_blank'
            ])
            ->linkToCrudAction('viewProduct')
        ;

        return $actions
            ->add(Crud::PAGE_EDIT, $viewProduct)
            ->add(Crud::PAGE_INDEX, $viewProduct)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            //->remove(Crud::PAGE_INDEX, Action::DELETE)
        ;
    }

    public function viewProduct(AdminContext $context): Response
    {
        /** @var Product $entity */
        $entity = $context->getEntity()->getInstance();

        return $this->redirectToRoute('product', [
            'slug' => $entity->getSlug()
        ]);
    }
}
