<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\PurchaseStateField;
use App\Entity\Data\Account;
use App\Entity\Data\Purchase;
use App\Entity\Traits\HasRoles;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\Translation\t;

class PurchaseCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly WorkflowInterface $purchaseStateMachine,
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Purchase::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('reference', t('Reference'))->onlyOnDetail();
        yield AssociationField::new('account', t('Point account'))
            ->setFormTypeOption('choice_label', fn (Account $account) => $account->__toString())
            ->setCrudController(AccountCrudController::class)
        ;
        yield AssociationField::new('wallet', t('Wallet'))
            ->setCrudController(WalletCrudController::class)
            ->hideOnForm()
        ;
        yield DateTimeField::new('createdAt', t('Date'))->hideOnForm();
        yield IntegerField::new('points', t('Points'));
        yield ChoiceField::new('mode', t('Method of payment'))
            ->setChoices([
                Purchase::MODE_CHECK => Purchase::MODE_CHECK,
                Purchase::MODE_BANK_WIRE => Purchase::MODE_BANK_WIRE,
            ])
            ->hideOnIndex()
        ;
        yield TextField::new('internReference', t('Internal Reference'))->hideOnIndex();
        yield PurchaseStateField::new('state', t('Status'))->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setFormOptions([
                'validation_groups' => ['new'],
            ])
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Point Purchase'))
            ->setEntityLabelInPlural(t('Point Purchases'))
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm:ss')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $purchaseStateMachine = $this->purchaseStateMachine;
        $actionCallback = function (Action $action) use ($purchaseStateMachine) {
            return $action->displayIf(
                fn (Purchase $purchase) => $purchaseStateMachine->getMarking($purchase)->has('pending')
            );
        };

        $cancel = Action::new('cancel', t('Cancel'))
            ->addCssClass('text-warning')
            ->displayIf(fn (Purchase $purchase) => $purchaseStateMachine->can($purchase, 'cancel'))
            ->displayAsLink()
            ->linkToRoute('point_purchase_cancel', fn (Purchase $purchase) => ['id' => $purchase->getId()])
        ;

        $refuse = Action::new('refuse', t('Refuse'))
            ->addCssClass('text-danger')
            ->displayIf(fn (Purchase $purchase) => $purchaseStateMachine->can($purchase, 'refuse'))
            ->displayAsLink()
            ->linkToRoute('point_purchase_refuse', fn (Purchase $purchase) => ['id' => $purchase->getId()])
        ;

        $accept = Action::new('accept', t('Accept'))
            ->addCssClass('text-success')
            ->displayIf(fn (Purchase $purchase) => $purchaseStateMachine->can($purchase, 'accept'))
            ->displayAsLink()
            ->linkToRoute('point_purchase_accept', fn (Purchase $purchase) => ['id' => $purchase->getId()])
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $cancel)
            ->add(Crud::PAGE_DETAIL, $cancel)
            ->add(Crud::PAGE_INDEX, $refuse)
            ->add(Crud::PAGE_DETAIL, $refuse)
            ->add(Crud::PAGE_INDEX, $accept)
            ->add(Crud::PAGE_DETAIL, $accept)
            ->update(Crud::PAGE_DETAIL, Action::DELETE, $actionCallback)
            ->update(Crud::PAGE_INDEX, Action::DELETE, $actionCallback)
            ->update(Crud::PAGE_DETAIL, Action::EDIT, $actionCallback)
            ->update(Crud::PAGE_INDEX, Action::EDIT, $actionCallback)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('account', t('Point account')))
            ->add(DateTimeFilter::new('createdAt', t('Creation date')))
        ;
    }

    #[Route(path: '/%website_admin_dashboard_path%/point/purchases/{id}/cancel', name: 'admin_dashboard_point_purchase_cancel', methods: ['GET'])]
    public function cancel(Purchase $purchase, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $this->purchaseStateMachine->apply($purchase, 'cancel');
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Points purchase canceled successfully.'));

        return $this->redirect(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($purchase->getId())
                ->generateUrl()
        );
    }

    #[Route(path: '/%website_admin_dashboard_path%/point/purchases/{id}/refuse', name: 'admin_dashboard_point_purchase_refuse', methods: ['GET'])]
    public function refuse(Purchase $purchase, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $this->purchaseStateMachine->apply($purchase, 'refuse');
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Points purchase rejected successfully.'));

        return $this->redirect(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($purchase->getId())
                ->generateUrl()
        );
    }

    #[Route(path: '/%website_admin_dashboard_path%/point/purchases/{id}/accept', name: 'admin_dashboard_point_purchase_accept', methods: ['GET'])]
    public function accept(Purchase $purchase, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $this->purchaseStateMachine->apply($purchase, 'accept');
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Point purchase accepted successfully, account has been credited successfully.'));

        return $this->redirect(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($purchase->getId())
                ->generateUrl()
        );
    }
}
