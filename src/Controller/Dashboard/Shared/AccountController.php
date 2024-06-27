<?php

namespace App\Controller\Dashboard\Shared;

use App\Controller\BaseController;
use App\Entity\Traits\HasRoles;
use App\Form\AccountEditFormType;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Contracts\Translation\TranslatorInterface;

/** My Profile User */
#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
#[IsGranted(HasRoles::DEFAULT)]
class AccountController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/profile', name: 'profile', methods: ['GET'])]
    public function accountProfile(): Response
    {
        return $this->render('dashboard/shared/account/profile.html.twig');
    }

    #[Route(path: '/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function accountEdit(Request $request): Response
    {
        $user = $this->getUserOrThrow();
        $form = $this->createForm(AccountEditFormType::class, $user)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->flush();

                $this->addFlash('success', $this->translator->trans('Your personal information has been modified successfully.'));

                return $this->redirectToRoute('dashboard_account_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/shared/account/edit.html.twig', compact('form', 'user'));
    }

    #[Route(path: '/change-password', name: 'change_password', methods: ['GET', 'POST'])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $hasher,
        LogoutUrlGenerator $logoutUrlGenerator
    ): Response {
        $user = $this->getUserOrThrow();

        $form = $this->createForm(ChangePasswordFormType::class, null, [
            'current_password_is_required' => true,
            'method' => 'PATCH',
        ])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $user->setPassword($hasher->hashPassword($user, $form['newPassword']->getData()));
                $this->em->flush();

                $this->addFlash('success', $this->translator->trans('Your password has been successfully changed.'));

                return $this->redirect($logoutUrlGenerator->getLogoutPath());
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/shared/account/change_password.html.twig', compact('form'));
    }
}
