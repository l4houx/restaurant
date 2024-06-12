<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\RolesField;
use App\Controller\Admin\Traits\ReadOnlyTrait;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Service\GeneratorTokenService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\Translation\t;

/**
 * @method User getUser()
 */
class UserCrudController extends AbstractCrudController
{
    use ReadOnlyTrait;

    public function __construct(
        private readonly EntityRepository $entityRepo,
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $userId = $this->getUser()->getId();

        $response = $this->entityRepo->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $response->andWhere('entity.id != :userId')->setParameter('userId', $userId);

        return $response;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('User'))
            ->setEntityLabelInPlural(t('Users'))
            ->setDefaultSort(['firstname' => 'ASC', 'lastname' => 'ASC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $logAs = Action::new('logAs', t('Login as'))
            ->displayAsLink()
            ->linkToRoute('home', fn (User $user) => ['_switch_user' => $user->getEmail()])
        ;

        $reset = Action::new('reset', t('Reset'))
            ->displayAsLink()
            ->linkToRoute('admin_dashboard_user_reset', fn (User $user) => ['user' => $user->getId()])
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $logAs)
            ->add(Crud::PAGE_DETAIL, $logAs)
            ->add(Crud::PAGE_INDEX, $reset)
            ->add(Crud::PAGE_DETAIL, $reset)

            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Avatar'))->hideOnForm();
        yield TextField::new('avatar')->hideOnForm();

        yield FormField::addPanel(t('User'));
        yield TextField::new('firstname', t('Firstname'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 2, max: 20),
            ])
        ;
        yield TextField::new('lastname', t('Lastname'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 2, max: 20),
            ])
        ;
        yield TextField::new('username', t('Username'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 4, max: 30),
            ])
        ;
        yield TextField::new('password', t('Password'))
            ->setFormType(PasswordType::class)
            ->setFormTypeOption('constraints', [
                new NotBlank(),
            ])
            ->onlyWhenCreating()
        ;
        yield EmailField::new('email', t('Email'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new NotNull(),
                new Email(),
                new Length(min: 5, max: 180),
            ])
        ;

        /*
        yield ChoiceField::new('roles', t('Roles'))
            ->renderExpanded()
            ->renderAsBadges([
                HasRoles::SUPERADMIN => 'danger',
                HasRoles::ADMIN => 'primary',
                HasRoles::MODERATOR => 'secondary',
                HasRoles::DEFAULT => 'info',
            ])
            ->setChoices([
                'Super Admin' => HasRoles::SUPERADMIN,
                'Admin' => HasRoles::ADMIN,
                'Moderator' => HasRoles::MODERATOR,
                'User' => HasRoles::DEFAULT,
            ])
            ->allowMultipleChoices()
            ->setRequired(isRequired: false)
        ;
        */

        yield RolesField::new('role', 'Role')
            ->hideOnForm()
        ;

        yield FormField::addPanel(t('Team'));
        yield BooleanField::new('isTeam', t('Team'))->hideOnIndex();
        yield TextareaField::new('about', t('About'))->hideOnIndex();
        yield TextareaField::new('designation', t('Designation'))->hideOnIndex();

        yield FormField::addPanel(t('Details'));
        yield BooleanField::new('suspended', t('Suspended'))->hideOnIndex();
        yield DateTimeField::new('bannedAt', t('Banner'))->hideOnForm();
        yield DateTimeField::new('lastLogin', t('Last connection'))->hideOnForm();
        yield TextField::new('lastLoginIp', t('IP'))->hideOnForm();

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deleted At'))->hideOnForm()->hideOnIndex();
    }

    #[Route(path: '/%website_admin_dashboard_path%/users/{id}/reset', name: 'admin_dashboard_user_reset', methods: ['GET'])]
    public function reset(
        User $user,
        TranslatorInterface $translator,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        GeneratorTokenService $generatorTokenService,
        AdminUrlGenerator $adminUrlGenerator
    ): RedirectResponse {
        // $password = md5(random_bytes(16));
        $password = $generatorTokenService->generateToken();
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $em->flush();

        $mailer->send(
            (new TemplatedEmail())
                ->from(new Address(
                    $this->getParameter('website_no_reply_email'),
                    $this->getParameter('website_name'),
                ))
                ->to(new Address($user->getEmail(), $user->getFullName()))
                ->htmlTemplate('mails/reset.html.twig')
                ->context(['customer' => $user, 'password' => $password])
        );

        $this->addFlash(
            'success',
            sprintf(
                $translator->trans('A new password has been generated and sent to %s.'),
                $user->getFullName()
            )
        );

        return $this->redirect(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }
}
