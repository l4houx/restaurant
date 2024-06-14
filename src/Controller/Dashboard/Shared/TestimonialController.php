<?php

namespace App\Controller\Dashboard\Shared;

use App\Entity\User;
use App\Entity\Testimonial;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Service\SettingService;
use App\Form\TestimonialFormType;
use App\Controller\BaseController;
use App\Security\Voter\TestimonialVoter;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TestimonialRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
#[IsGranted(HasRoles::DEFAULT)]
class TestimonialController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly TestimonialRepository $testimonialRepository,
        private readonly Security $security,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '/my-testimonials', name: 'testimonial_index', methods: ['GET'])]
    public function index(Request $request, #[CurrentUser] User $user, PaginatorInterface $paginator): Response
    {
        $keyword = '' == $request->query->get('keyword') ? 'all' : $request->query->get('keyword');
        $isOnline = '' == $request->query->get('isOnline') ? 'all' : $request->query->get('isOnline');
        $rating = '' == $request->query->get('rating') ? 'all' : $request->query->get('rating');
        $slug = '' == $request->query->get('slug') ? 'all' : $request->query->get('slug');
        $user = '' == $request->query->get('user') ? 'all' : $request->query->get('user');

        $testimonials = $paginator->paginate(
            $this->settingService->getTestimonials(['user' => $user, 'keyword' => $keyword, 'slug' => $slug, 'isOnline' => $isOnline, 'rating' => $rating])->getQuery(),
            $request->query->getInt('page', 1),
            HasLimit::TESTIMONIAL_LIMIT,
            [
                'wrap-queries' => true
            ]
        );

        return $this->render('dashboard/shared/testimonials/index.html.twig', compact('testimonials', 'user'));
    }

    /*
    public function index(Request $request, #[CurrentUser] User $user): Response
    {
        $page = $request->query->getInt('page', 1);
        $testimonials = $this->testimonialRepository->findForPagination($page);

        return $this->render('dashboard/shared/testimonials/index.html.twig', compact('testimonials', 'user'));
    }
    */

    #[Route(path: '/my-testimonials/new', name: 'testimonial_new', methods: ['GET', 'POST'])]
    public function newedit(Request $request, #[CurrentUser] User $user, ?string $slug = null): Response
    {
        if (!$slug) {
            $testimonial = new Testimonial();
            $testimonial->setAuthor($user);
            $this->denyAccessUnlessGranted(TestimonialVoter::CREATE);
        } else {
            /** @var Testimonial $testimonial */
            $testimonial = $this->settingService->getTestimonials(['isOnline' => 'all', 'slug' => $slug])->getQuery()->getOneOrNullResult();
            if (!$testimonial) {
                $this->addFlash('danger', $this->translator->trans('The testimonial can not be found'));

                if ($this->security->isGranted(HasRoles::ADMINAPPLICATION)) {
                    return $this->redirectToRoute('dashboard_admin_testimonial_index', [], Response::HTTP_SEE_OTHER);
                } elseif ($this->security->isGranted(HasRoles::DEFAULT)) {
                    return $this->redirectToRoute('dashboard_user_testimonial_index', [], Response::HTTP_SEE_OTHER);
                }
            }
            $this->denyAccessUnlessGranted(TestimonialVoter::MANAGE, $testimonial, 'Testimonials can only be shown to their authors.');
        }

        $form = $this->createForm(TestimonialFormType::class, $testimonial)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($this->security->isGranted(HasRoles::ADMINAPPLICATION)) {
                    $testimonial->setIsOnline(true);
                } elseif ($this->security->isGranted(HasRoles::DEFAULT)) {
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

                if ($this->security->isGranted(HasRoles::ADMINAPPLICATION)) {
                    return $this->redirectToRoute('dashboard_admin_testimonial_index', [], Response::HTTP_SEE_OTHER);
                } elseif ($this->security->isGranted(HasRoles::DEFAULT)) {
                    return $this->redirectToRoute('dashboard_user_testimonial_index', [], Response::HTTP_SEE_OTHER);
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/shared/testimonials/new-edit.html.twig', compact('form', 'testimonial', 'user'));
    }

    #[Route(path: '/my-testimonials/{slug}', name: 'testimonial_view', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function view(Testimonial $testimonial): Response
    {
        return $this->render('dashboard/shared/testimonials/view.html.twig', compact('testimonial'));
    }
}
