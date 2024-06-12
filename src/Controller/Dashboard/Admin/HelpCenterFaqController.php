<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\HelpCenterFaq;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Service\SettingService;
use App\Form\HelpCenterFaqFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HelpCenterFaqRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/%website_dashboard_path%/admin/manage-help-center', name: 'dashboard_admin_help_center_faq_')]
#[IsGranted(HasRoles::ADMINAPPLICATION)]
class HelpCenterFaqController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $em,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $keyword = '' === $request->query->get('keyword') ? 'all' : $request->query->get('keyword');

        $rows = $paginator->paginate($this->settingService->getHelpCenterFaqs(['keyword' => $keyword, 'isOnline' => 'all', 'sort' => 'updatedAt', 'order' => 'DESC']), $request->query->getInt('page', 1), HasLimit::HELPCENTERFAQ_LIMIT, ['wrap-queries' => true]);

        return $this->render('dashboard/admin/helpCenter/faqs/index.html.twig', compact('rows'));
    }

    /*
    public function index(Request $request, PaginatorInterface $paginator, HelpCenterFaqRepository $helpCenterFaqRepository): Response
    {
        $query = $helpCenterFaqRepository->findBy(['isOnline' => true], ['updatedAt' => 'DESC']);
        $page = $request->query->getInt('page', 1);

        $rows = $paginator->paginate($query, $page, HasLimit::HELPCENTERFAQ_LIMIT, ['wrap-queries' => true]);

        return $this->render('dashboard/admin/helpCenter/faqs/index.html.twig', compact('rows'));
    }
    */

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function newedit(Request $request, ?string $id = null): Response
    {
        if (!$id) {
            $faq = new HelpCenterFaq();
        } else {
            /** @var HelpCenterFaq $faq */
            $faq = $this->settingService->getHelpCenterFaqs(['isOnline' => 'all', 'id' => $id])->getQuery()->getOneOrNullResult();
            if (!$faq) {
                $this->addFlash('danger', $this->translator->trans('The faq can not be found'));

                return $this->redirectToRoute('dashboard_admin_help_center_faq_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(HelpCenterFaqFormType::class, $faq)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($faq);
                $this->em->flush();
                if (!$id) {
                    $this->addFlash('success', $this->translator->trans('Content was created successfully.'));
                } else {
                    $this->addFlash('info', $this->translator->trans('Content was edited successfully.'));
                }

                return $this->redirectToRoute('dashboard_admin_help_center_faq_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/helpCenter/faqs/new-edit.html.twig', compact('form', 'faq'));
    }

    #[Route(path: '/{id}', name: 'view', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function view(HelpCenterFaq $faq): Response
    {
        return $this->render('dashboard/admin/helpCenter/faqs/view.html.twig', compact('faq'));
    }

    #[Route(path: '/{id}/disable', name: 'disable', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[Route(path: '/{id}/delete', name: 'delete', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(string $id): Response
    {
        /** @var HelpCenterFaq $faq */
        $faq = $this->settingService->getHelpCenterFaqs(['isOnline' => 'all', 'id' => $id])->getQuery()->getOneOrNullResult();
        if (!$faq) {
            $this->addFlash('danger', $this->translator->trans('The faq can not be found'));

            return $this->redirectToRoute('dashboard_admin_help_center_faq_index', [], Response::HTTP_SEE_OTHER);
        }
        if (null !== $faq->getDeletedAt()) {
            $this->addFlash('danger', $this->translator->trans('Content was deleted successfully.'));
        } else {
            $this->addFlash('danger', $this->translator->trans('Content was disabled successfully.'));
        }

        $faq->setIsOnline(true);

        $this->em->persist($faq);
        $this->em->flush();
        $this->em->remove($faq);
        $this->em->flush();

        return $this->redirectToRoute('dashboard_admin_help_center_faq_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/restore', name: 'restore', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function restore(string $id): Response
    {
        /** @var HelpCenterFaq $faq */
        $faq = $this->settingService->getHelpCenterFaqs(['isOnline' => 'all', 'id' => $id])->getQuery()->getOneOrNullResult();
        if (!$faq) {
            $this->addFlash('danger', $this->translator->trans('The faq can not be found'));

            return $this->redirectToRoute('dashboard_admin_help_center_faq_index', [], Response::HTTP_SEE_OTHER);
        }
        $faq->setDeletedAt(null);

        $this->em->persist($faq);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Content was restored successfully.'));

        return $this->redirectToRoute('dashboard_admin_help_center_faq_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/show', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[Route(path: '/{id}/hide', name: 'hide', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function showhide(string $id): Response
    {
        /** @var HelpCenterFaq $faq */
        $faq = $this->settingService->getHelpCenterFaqs(['isOnline' => 'all', 'id' => $id])->getQuery()->getOneOrNullResult();
        if (!$faq) {
            $this->addFlash('danger', $this->translator->trans('The faq can not be found'));

            return $this->redirectToRoute('dashboard_admin_help_center_faq_index', [], Response::HTTP_SEE_OTHER);
        }

        if (false === $faq->getIsOnline()) {
            $faq->setIsOnline(true);
            $this->addFlash('success', $this->translator->trans('Content is online'));
        } else {
            $faq->setIsOnline(false);
            $this->addFlash('danger', $this->translator->trans('Content is offline'));
        }

        $this->em->persist($faq);
        $this->em->flush();

        return $this->redirectToRoute('dashboard_admin_help_center_faq_index', [], Response::HTTP_SEE_OTHER);
    }
}
