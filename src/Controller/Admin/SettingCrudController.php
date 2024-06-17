<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateEditTrait;
use App\Entity\Setting;
use App\Entity\Traits\HasRoles;
use App\Repository\SettingRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\EntityCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

use function Symfony\Component\Translation\t;

class SettingCrudController extends AbstractCrudController
{
    use CreateEditTrait;

    public function __construct(
        private SettingRepository $settingRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Setting::class;
    }

    public function createEditForm(
        EntityDto $entityDto,
        KeyValueStore $formOptions,
        AdminContext $context
    ): FormInterface {
        $formBuilder = parent::createEditForm($entityDto, $formOptions, $context);

        $value = $formBuilder->getViewData()->getValue();
        $type = $formBuilder->get('type')->getData();

        $formBuilder->add('value', $type, [
            'data' => CheckboxType::class === $type ? (bool) $value : $value,
        ]);

        return $formBuilder;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        return $this->settingRepository->getIndexQueryBuilder();
    }

    public function index(AdminContext $context): KeyValueStore|Response
    {
        if (!$this->isGranted(HasRoles::ADMINAPPLICATION)) {
            return $this->redirectToRoute('signin');
        }

        $response = parent::index($context);

        if ($response instanceof Response) {
            return $response;
        }

        /** @var EntityCollection $entities */
        $entities = $response->get('entities');

        foreach ($entities as $entity) {
            $fields = $entity->getFields();

            $valueField = $fields->getByProperty('value');
            $typeField = $fields->getByProperty('type');

            $type = $typeField->getValue();

            $valueField->setFormType($type);

            $entity->getFields()->unset($typeField);
        }

        $response->set('entities', $entities);

        return $response;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('General settings'));
        yield IdField::new('id')->onlyOnIndex();
        /*
        yield TextField::new('label', t('Option'))
            ->setFormTypeOption('attr', [
                'readonly' => true,
            ])
            ->setSortable(false)
        ;
        */

        yield TextField::new('label', t('Label'))->setHelp(t('For example : (Site Name)'));
        if (Crud::PAGE_NEW === $pageName) {
            yield TextField::new('name', t('Name (Key)'))
                ->onlyOnForms()
                ->setRequired(true)
                ->setHelp(t('For example : (website_name)'))
            ;
        } else {
            yield TextField::new('name', t('Name (Key)'))
                ->setFormTypeOption('attr', [
                    'readonly' => true,
                ])
                ->setSortable(false)
            ;
        }
        yield TextField::new('value', t('Value'))->setHelp(t('For example : (Yourname)'));

        if (Crud::PAGE_NEW === $pageName) {
            yield TextField::new('type', t('Type'))
                ->setHelp(t('For example : (Symfony\Component\Form\Extension\Core\Type\TextType)'))
            ;
        } else {
            yield TextField::new('type')
                ->setFormTypeOption('attr', [
                    'readonly' => true,
                ])
                ->setSortable(false)
            ;
        }

        yield FormField::addPanel(t('Date'))->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Setting'))
            ->setEntityLabelInPlural(t('Settings'))
            ->setDefaultSort(['id' => 'DESC', 'name' => 'ASC'])
            // ->setSearchFields(null)
            // ->showEntityActionsInlined()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('value')
            ->add('id')
            ->add('name')
        ;
    }
}
