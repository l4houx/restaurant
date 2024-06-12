<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Page;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Form\PageFormType;
use App\Repository\PageRepository;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/%website_dashboard_path%/admin/manage-pages', name: 'dashboard_admin_page_')]
#[IsGranted(HasRoles::ADMINAPPLICATION)]
class PagesController extends AdminBaseController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly PageRepository $pageRepository,
        private readonly EntityManagerInterface $em,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $rows = $paginator->paginate($this->settingService->getPages([]), $request->query->getInt('page', 1), HasLimit::PAGE_LIMIT, ['wrap-queries' => true]);

        return $this->render('dashboard/admin/pages/index.html.twig', compact('rows'));
    }

    /*
    public function index(Request $request, #[CurrentUser] User $user): Response
    {
        $page = $request->query->getInt('page', 1);
        $rows = $this->pageRepository->findForPagination($page);

        return $this->render('dashboard/admin/pages/index.html.twig', compact('rows'));
    }
    */

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function newedit(Request $request, ?string $slug = null): Response
    {
        if (!$slug) {
            $page = new Page();
        } else {
            /** @var Page $page */
            $page = $this->settingService->getPages(['slug' => $slug])->getQuery()->getOneOrNullResult();
            /** @var Page $page */
            if (!$page) {
                $this->addFlash('danger', $this->translator->trans('The page can not be found'));

                return $this->redirectToRoute('dashboard_admin_page_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(PageFormType::class, $page)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($page);
                $this->em->flush();
                if (!$slug) {
                    $this->addFlash('success', $this->translator->trans('Content was created successfully.'));
                } else {
                    $this->addFlash('info', $this->translator->trans('Content was edited successfully.'));
                }

                return $this->redirectToRoute('dashboard_admin_page_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/pages/new-edit.html.twig', compact('page', 'form'));
    }

    #[Route(path: '/{slug}', name: 'view', methods: ['GET'])]
    public function view(Page $page): Response
    {
        return $this->render('dashboard/admin/pages/view.html.twig', compact('page'));
    }

    #[Route(path: '/{slug}/delete', name: 'delete', methods: ['GET'])]
    public function delete(Request $request, Page $page): Response
    {
        if (!$page) {
            $this->addFlash('danger', $this->translator->trans('The page can not be found'));

            return $this->redirectToRoute('dashboard_admin_page_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('page_deletion_'.$page->getSlug(), $request->getPayload()->get('_token'))) {
            $this->em->remove($page);
            $this->em->flush();

            $this->addFlash(
                'danger',
                sprintf(
                    $this->translator->trans('Content %s was deleted successfully.'),
                    $page->getName()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_page_index', [], Response::HTTP_SEE_OTHER);
    }
}
