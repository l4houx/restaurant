<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Review;
use App\Entity\Comment;
use App\Entity\Setting;
use App\Entity\Currency;
use App\Entity\PostType;
use App\Entity\Question;
use App\Entity\Testimonial;
use App\Entity\PostCategory;
use App\Entity\Traits\HasRoles;
use App\Entity\AppLayoutSetting;
use App\Entity\HelpCenterArticle;
use App\Entity\HelpCenterCategory;
use App\Entity\HomepageHeroSetting;
use function Symfony\Component\Translation\t;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route(path: '/%website_admin_dashboard_path%', name: 'admin_dashboard_index')]
    #[IsGranted(HasRoles::TEAM)]
    public function index(): Response
    {
        $controller = $this->isGranted(HasRoles::ADMIN) ? SettingCrudController::class : PostCrudController::class;

        $url = $this->adminUrlGenerator
            ->setController($controller)
            ->generateUrl()
        ;

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->getParameter('website_name'))
            ->setTranslationDomain('admin')
            ->setTextDirection('ltr')
            ->renderContentMaximized()
            ->renderSidebarMinimized()
            ->generateRelativeUrls()
            ->setLocales([
                'en', 'fr',
            ])
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl(t('Visit public website'), 'fas fa-undo', '/');

        yield MenuItem::linkToDashboard(t('Dashboard'), 'fa fa-home');

        if ($this->isGranted(HasRoles::ADMINAPPLICATION)) {
            yield MenuItem::section(t('General settings'));
            yield MenuItem::subMenu(t('General'), 'fa fa-wrench')->setSubItems([
                MenuItem::linkToCrud(t('Settings'), 'fas fa-cog', Setting::class),
                MenuItem::linkToCrud(t('Layout settings'), 'fas fa-images', AppLayoutSetting::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', AppLayoutSetting::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Heros settings'), 'fas fa-image', HomepageHeroSetting::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', HomepageHeroSetting::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Currency settings'), 'fas fa-money-check-dollar', Currency::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Currency::class)->setAction(Crud::PAGE_NEW),
            ]);
        }

        if ($this->isGranted(HasRoles::EDITOR)) {
            yield MenuItem::section(t('Blog Settings'));
            yield MenuItem::subMenu(t('Post Settings'), 'fas fa-newspaper')->setSubItems([
                MenuItem::linkToCrud(t('Post'), 'fas fa-newspaper', Post::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Post::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Category'), 'fab fa-delicious', PostCategory::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', PostCategory::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Type'), 'fas fa-bars', PostType::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', PostType::class)->setAction(Crud::PAGE_NEW),
            ]);

            yield MenuItem::section(t('Comment Settings'));
            yield MenuItem::linkToCrud(t('Comments'), 'fas fa-comment', Comment::class);

            yield MenuItem::section(t('Review Settings'));
            yield MenuItem::subMenu(t('Testimonial Settings'), 'fas fa-star')->setSubItems([
                // MenuItem::linkToCrud(t('Review'), 'fas fa-star', Review::class),
                // MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Review::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Testimonial'), 'fab fa-delicious', Testimonial::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Testimonial::class)->setAction(Crud::PAGE_NEW),
            ]);

            yield MenuItem::section(t('Pages Settings'));
            yield MenuItem::subMenu(t('Pages'), 'fas fa-file')->setSubItems([
                MenuItem::linkToCrud(t('All pages'), 'fas fa-file', Page::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Page::class)->setAction(Crud::PAGE_NEW),
            ]);

            yield MenuItem::section(t('Question Settings'));
            yield MenuItem::subMenu(t('Question'), 'fa fa-question-circle')->setSubItems([
                MenuItem::linkToCrud(t('All questions'), 'fa fa-question-circle', Question::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Question::class)->setAction(Crud::PAGE_NEW),
            ]);

            /*
            yield MenuItem::section(t('Help Center Settings'));
            yield MenuItem::subMenu(t('Help Center'), 'fas fa-question')->setSubItems([
                MenuItem::linkToCrud(t('All articles'), 'fas fa-question', HelpCenterArticle::class),
                MenuItem::linkToCrud(t('All categories'), 'fas fa-list', HelpCenterCategory::class),
            ]);
            */

            yield MenuItem::section(t('Users Settings'));
            yield MenuItem::subMenu(t('All accounts'), 'fas fa-user')->setSubItems([
                MenuItem::linkToCrud(t('Users'), 'fas fa-user-friends', User::class),
                MenuItem::linkToExitImpersonation(t('Stop impersonation'), 'fas fa-door-open'),
            ]);

            //yield MenuItem::section(t('Contact Settings'));
            //yield MenuItem::linkToCrud(t('Contact'), 'fas fa-message', Contact::class);
        }

        yield MenuItem::section();
        yield MenuItem::linkToLogout(t('Sign Out'), 'fa fa-sign-out');
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->renderContentMaximized()
            ->showEntityActionsInlined()
            ->setDefaultSort([
                'id' => 'DESC',
            ])
        ;
    }

    public function configureUserMenu(UserInterface|User $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getUsername())
            ->displayUserName(false)
            ->addMenuItems([
                MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
                MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
                // MenuItem::section(),
                // MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ])
        ;
    }
}
