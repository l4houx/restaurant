<?php

namespace App\Controller\Dashboard\Shared;

use App\Controller\BaseController;
use App\Entity\Traits\HasRoles;
use App\Form\ClientAccountFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/** My Profile Client (User) */
#[Route(path: '/%website_dashboard_path%/user', name: 'dashboard_user_')]
#[IsGranted(HasRoles::DEFAULT)]
class ClientAccountController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/client-profile', name: 'client_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $user = $this->getUserOrThrow();
        $form = $this->createForm(ClientAccountFormType::class, $this->getUser())->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($this->getUser());
                $this->em->flush();
                $this->addFlash('info', $this->translator->trans('Content was edited successfully.'));

                return $this->redirectToRoute('dashboard_user_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/shared/client/index.html.twig', compact('form', 'user'));
    }
}
