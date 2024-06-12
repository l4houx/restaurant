<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\PostCategory;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Service\SettingService;
use App\Form\PostCategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PostCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/%website_dashboard_path%/admin/manage-posts-categories', name: 'dashboard_admin_post_category_')]
#[IsGranted(HasRoles::ADMINAPPLICATION)]
class PostCategoryController extends AdminBaseController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly PostCategoryRepository $postCategoryRepository,
        private readonly EntityManagerInterface $em,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $keyword = '' == $request->query->get('keyword') ? 'all' : $request->query->get('keyword');

        $rows = $paginator->paginate($this->settingService->getBlogPostCategories(['keyword' => $keyword, 'isOnline' => 'all', 'order' => 'c.createdAt', 'sort' => 'DESC']), $request->query->getInt('page', 1), HasLimit::POSTCATEGORY_LIMIT, ['wrap-queries' => true]);

        return $this->render('dashboard/admin/post/category/index.html.twig', compact('rows'));
    }

    /*
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $rows = $this->postCategoryRepository->findForPagination($page);

        return $this->render('dashboard/admin/post/category/index.html.twig', compact('rows'));
    }
    */

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/{slug}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function newedit(Request $request, ?string $slug = null): Response
    {
        if (!$slug) {
            $postcategory = new PostCategory();
        } else {
            /** @var PostCategory $postcategory */
            $postcategory = $this->settingService->getBlogPostCategories(['isOnline' => 'all', 'slug' => $slug])->getQuery()->getOneOrNullResult();

            if (!$postcategory) {
                $this->addFlash('danger', $this->translator->trans('The article category can not be found'));

                return $this->redirectToRoute('dashboard_admin_post_category_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(PostCategoryFormType::class, $postcategory)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($postcategory);
                $this->em->flush();
                if (!$slug) {
                    $this->addFlash('success', $this->translator->trans('Content was created successfully.'));
                } else {
                    $this->addFlash('info', $this->translator->trans('Content was edited successfully.'));
                }

                return $this->redirectToRoute('dashboard_admin_post_category_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/post/category/new-edit.html.twig', compact('form', 'postcategory'));
    }

    #[Route(path: '/{slug}', name: 'view', methods: ['GET'])]
    public function view(PostCategory $postcategory): Response
    {
        return $this->render('dashboard/admin/post/category/view.html.twig', compact('postcategory'));
    }

    #[Route(path: '/{slug}/disable', name: 'disable', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    #[Route(path: '/{slug}/delete', name: 'delete', methods: ['POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function delete(string $slug): Response
    {
        /** @var PostCategory $postcategory */
        $postcategory = $this->settingService->getBlogPostCategories(['isOnline' => 'all', 'slug' => $slug])->getQuery()->getOneOrNullResult();

        if (!$postcategory) {
            $this->addFlash('danger', $this->translator->trans('The article category can not be found'));

            return $this->redirectToRoute('dashboard_admin_post_category_index', [], Response::HTTP_SEE_OTHER);
        }

        if (count($postcategory->getPosts()) > 0) {
            $this->addFlash('danger', $this->translator->trans('The post category can not be deleted because it is linked with one or more posts'));

            return $this->redirectToRoute('dashboard_admin_post_category_index', [], Response::HTTP_SEE_OTHER);
        }

        if (null !== $postcategory->getDeletedAt()) {
            $this->addFlash('danger', $this->translator->trans('Content was deleted successfully.'));
        } else {
            $this->addFlash('danger', $this->translator->trans('Content was disabled successfully.'));
        }

        $postcategory->setIsOnline(true);

        $this->em->persist($postcategory);
        $this->em->flush();
        $this->em->remove($postcategory);
        $this->em->flush();

        return $this->redirectToRoute('dashboard_admin_post_category_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{slug}/restore', name: 'restore', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function restore(string $slug): Response
    {
        /** @var PostCategory $postcategory */
        $postcategory = $this->settingService->getBlogPostCategories(['isOnline' => 'all', 'slug' => $slug])->getQuery()->getOneOrNullResult();
        if (!$postcategory) {
            $this->addFlash('danger', $this->translator->trans('The article category can not be found'));

            return $this->redirectToRoute('dashboard_admin_post_category_index', [], Response::HTTP_SEE_OTHER);
        }

        $postcategory->setDeletedAt(null);

        $this->em->persist($postcategory);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Content was restored successfully.'));

        return $this->redirectToRoute('dashboard_admin_post_category_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{slug}/show', name: 'show', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    #[Route(path: '/{slug}/hide', name: 'hide', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function showhide(string $slug): Response
    {
        /** @var PostCategory $postcategory */
        $postcategory = $this->settingService->getBlogPostCategories(['isOnline' => 'all', 'slug' => $slug])->getQuery()->getOneOrNullResult();

        if (!$postcategory) {
            $this->addFlash('danger', $this->translator->trans('The article category can not be found'));

            return $this->redirectToRoute('dashboard_admin_post_category_index', [], Response::HTTP_SEE_OTHER);
        }

        if (false === $postcategory->getIsOnline()) {
            $postcategory->setIsOnline(true);
            $this->addFlash('success', $this->translator->trans('Content is online'));
        } else {
            $postcategory->setIsOnline(false);
            $this->addFlash('danger', $this->translator->trans('Content is offline'));
        }

        $this->em->persist($postcategory);
        $this->em->flush();

        return $this->redirectToRoute('dashboard_admin_post_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
