<?php

namespace App\Controller\Dashboard\Shared;

use App\Controller\BaseController;
use App\Entity\Testimonial;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Form\TestimonialFormType;
use App\Repository\TestimonialRepository;
use App\Security\Voter\TestimonialVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
#[IsGranted(HasRoles::DEFAULT)]
class TestimonialController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly TestimonialRepository $testimonialRepository
    ) {
    }

    #[Route(path: '/my-testimonials', name: 'testimonial_index', methods: ['GET'])]
    public function index(#[CurrentUser] User $user): Response
    {
        return $this->render('dashboard/shared/testimonials/index.html.twig', [
            'user' => $user,
            'testimonials' => $this->testimonialRepository->getLastByUser($user),
        ]);
    }

    #[Route(path: '/my-testimonials/new', name: 'testimonial_new', methods: ['GET', 'POST'])]
    #[IsGranted(TestimonialVoter::CREATE)]
    public function new(Request $request, #[CurrentUser] User $user): Response
    {
        $testimonial = new Testimonial();
        $testimonial->setAuthor($user);

        $form = $this->createForm(TestimonialFormType::class, $testimonial)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $testimonial->setIsOnline(false);

                $this->em->persist($testimonial);
                $this->em->flush();

                $this->addFlash(
                    'success',
                    sprintf(
                        $this->translator->trans('Your testimonial %s has been sent, thank you. It will be published after validation by a moderator.'),
                        $testimonial->getAuthor()->getFullName()
                    )
                );

                return $this->redirectToRoute('dashboard_account_testimonial_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/shared/testimonials/new.html.twig', compact('form', 'testimonial', 'user'));
    }
}
