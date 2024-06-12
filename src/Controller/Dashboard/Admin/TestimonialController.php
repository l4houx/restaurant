<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Testimonial;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Form\TestimonialFormType;
use App\Repository\TestimonialRepository;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/%website_dashboard_path%/admin/manage-testimonials', name: 'dashboard_admin_testimonial_')]
#[IsGranted(HasRoles::ADMINAPPLICATION)]
class TestimonialController extends AdminBaseController
{
    public function __construct(
        private readonly TestimonialRepository $testimonialRepository,
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $em,
        private readonly SluggerInterface $slugger,
        private readonly AuthorizationCheckerInterface $authChecker,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $keyword = '' == $request->query->get('keyword') ? 'all' : $request->query->get('keyword');
        $isOnline = '' == $request->query->get('isOnline') ? 'all' : $request->query->get('isOnline');
        $rating = '' == $request->query->get('rating') ? 'all' : $request->query->get('rating');
        $slug = '' == $request->query->get('slug') ? 'all' : $request->query->get('slug');

        $user = 'all';
        if ($this->authChecker->isGranted(HasRoles::DEFAULT)) {
            $user = $this->getUser()->getId();
        }

        $rows = $paginator->paginate(
            $this->settingService->getTestimonials(['user' => $user, 'keyword' => $keyword, 'slug' => $slug, 'isOnline' => $isOnline, 'rating' => $rating])->getQuery(),
            $request->query->getInt('page', 1),
            HasLimit::TESTIMONIAL_LIMIT,
            ['wrap-queries' => true]
        );

        return $this->render('dashboard/admin/testimonials/index.html.twig', compact('rows'));
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/{slug}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function newedit(Request $request, #[CurrentUser] User $user, ?string $slug = null)
    {
        if (!$slug) {
            $testimonial = new Testimonial();
        } else {
            /** @var Testimonial $testimonial */
            $testimonial = $this->settingService->getTestimonials(['isOnline' => 'all', 'slug' => $slug])->getQuery()->getOneOrNullResult();
            if (!$testimonial) {
                $this->addFlash('danger', $this->translator->trans('The testimonial can not be found'));

                return $this->redirectToRoute('dashboard_admin_testimonial_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(TestimonialFormType::class, $testimonial)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $testimonial->setSlug($this->slugger->slug($testimonial->getName())->lower());
                $testimonial->setAuthor($user);
                if ($this->authChecker->isGranted(HasRoles::ADMIN)) {
                    $testimonial->setIsOnline(true);
                } elseif ($this->authChecker->isGranted(HasRoles::DEFAULT)) {
                    $testimonial->setIsOnline(false);
                }

                $this->em->persist($testimonial);
                $this->em->flush();

                if (!$slug) {
                    $this->addFlash(
                        'success',
                        sprintf(
                            $this->translator->trans('Content %s was created successfully.'),
                            $testimonial->getAuthor()->getFullName()
                        )
                    );
                } else {
                    $this->addFlash(
                        'info',
                        sprintf(
                            $this->translator->trans('Content %s was edited successfully.'),
                            $testimonial->getAuthor()->getFullName()
                        )
                    );
                }

                return $this->redirectToRoute('dashboard_admin_testimonial_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/testimonials/new-edit.html.twig', compact('form', 'testimonial'));
    }

    #[Route(path: '/{slug}', name: 'view', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function view(Testimonial $testimonial): Response
    {
        return $this->render('dashboard/admin/testimonials/view.html.twig', compact('testimonial'));
    }

    #[Route(path: '/{slug}/delete', name: 'delete', methods: ['POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function delete(Request $request, Testimonial $testimonial): Response
    {
        if ($this->isCsrfTokenValid('testimonial_deletion_'.$testimonial->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($testimonial);
            $this->em->flush();

            $this->addFlash(
                'danger',
                sprintf(
                    $this->translator->trans('Content %s was deleted successfully.'),
                    $testimonial->getAuthor()->getFullName()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_testimonial_index', [], Response::HTTP_SEE_OTHER);
    }
}
