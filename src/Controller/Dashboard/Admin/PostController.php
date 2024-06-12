<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Post;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Form\PostFormType;
use App\Repository\PostRepository;
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

#[Route(path: '/%website_dashboard_path%/admin/manage-posts', name: 'dashboard_admin_post_')]
#[IsGranted(HasRoles::ADMINAPPLICATION)]
class PostController extends AdminBaseController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly PostRepository $postRepository,
        private readonly EntityManagerInterface $em,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $keyword = '' == $request->query->get('keyword') ? 'all' : $request->query->get('keyword');

        $rows = $paginator->paginate($this->settingService->getBlogPosts(['keyword' => $keyword, 'isOnline' => 'all']), $request->query->getInt('page', 1), HasLimit::POST_LIMIT, ['wrap-queries' => true]);

        return $this->render('dashboard/admin/post/articles/index.html.twig', compact('rows'));
    }

    /*
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $rows = $this->postRepository->findForPagination($page);

        return $this->render('dashboard/admin/post/articles/index.html.twig', compact('rows'));
    }
    */

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/{slug}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function newedit(Request $request, ?string $slug = null, #[CurrentUser] User $user): Response
    {
        if (!$slug) {
            $post = new Post();
        // $post->setAuthor($user);
        } else {
            /** @var Post $post */
            $post = $this->settingService->getBlogPosts(['isOnline' => 'all', 'slug' => $slug])->getQuery()->getOneOrNullResult();
            if (!$post) {
                $this->addFlash('danger', $this->translator->trans('The article can not be found'));

                return $this->redirectToRoute('dashboard_admin_post_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(PostFormType::class, $post)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($post);
                $this->em->flush();
                if (!$slug) {
                    $this->addFlash('success', $this->translator->trans('Content was created successfully.'));
                } else {
                    $this->addFlash('info', $this->translator->trans('Content was edited successfully.'));
                }

                return $this->redirectToRoute('dashboard_admin_post_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/post/articles/new-edit.html.twig', compact('post', 'form'));
    }

    #[Route(path: '/{slug}', name: 'view', methods: ['GET'])]
    public function view(Post $post): Response
    {
        return $this->render('dashboard/admin/post/articles/view.html.twig', compact('post'));
    }

    #[Route(path: '/{slug}/disable', name: 'disable', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    #[Route(path: '/{slug}/delete', name: 'delete', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function delete(string $slug): Response
    {
        /** @var Post $post */
        $post = $this->settingService->getBlogPosts(['isOnline' => 'all', 'slug' => $slug])->getQuery()->getOneOrNullResult();
        if (!$post) {
            $this->addFlash('danger', $this->translator->trans('The article can not be found'));

            return $this->redirectToRoute('dashboard_admin_post_index', [], Response::HTTP_SEE_OTHER);
        }

        if (null !== $post->getDeletedAt()) {
            $this->addFlash('danger', $this->translator->trans('Content was deleted successfully.'));
        } else {
            $this->addFlash('danger', $this->translator->trans('Content was disabled successfully.'));
        }

        $post->setIsOnline(true);

        $this->em->persist($post);
        $this->em->flush();
        $this->em->remove($post);
        $this->em->flush();

        return $this->redirectToRoute('dashboard_admin_post_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{slug}/restore', name: 'restore', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function restore(string $slug): Response
    {
        /** @var Post $post */
        $post = $this->settingService->getBlogPosts(['isOnline' => 'all', 'slug' => $slug])->getQuery()->getOneOrNullResult();
        if (!$post) {
            $this->addFlash('danger', $this->translator->trans('The article can not be found'));

            return $this->redirectToRoute('dashboard_admin_post_index', [], Response::HTTP_SEE_OTHER);
        }

        $post->setDeletedAt(null);

        $this->em->persist($post);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Content was restored successfully.'));

        return $this->redirectToRoute('dashboard_admin_post_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{slug}/show', name: 'show', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    #[Route(path: '/{slug}/hide', name: 'hide', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function showhide(string $slug): Response
    {
        /** @var Post $post */
        $post = $this->settingService->getBlogPosts(['isOnline' => 'all', 'slug' => $slug])->getQuery()->getOneOrNullResult();

        if (!$post) {
            $this->addFlash('danger', $this->translator->trans('The article can not be found'));

            return $this->redirectToRoute('dashboard_admin_post_index', [], Response::HTTP_SEE_OTHER);
        }

        if (false === $post->getIsOnline()) {
            $post->setIsOnline(true);
            $this->addFlash('success', $this->translator->trans('Content is online'));
        } else {
            $post->setIsOnline(false);
            $this->addFlash('danger', $this->translator->trans('Content is offline'));
        }

        $this->em->persist($post);
        $this->em->flush();

        return $this->redirectToRoute('dashboard_admin_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
