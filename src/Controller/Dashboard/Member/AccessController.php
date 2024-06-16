<?php

namespace App\Controller\Dashboard\Member;

use App\Entity\User;
use App\Entity\User\Manager;
use App\Service\AvatarService;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Entity\User\SalesPerson;
use App\Entity\User\Collaborator;
use App\Controller\BaseController;
use App\Repository\UserRepository;
use App\Form\Member\AccessFormType;
use App\Form\FilterFormType;
use Symfony\Component\Mime\Address;
use App\Entity\User\SuperAdministrator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted(HasRoles::MANAGER)]
#[Route(path: '/%website_dashboard_path%/member/access', name: 'dashboard_member_access_')]
class AccessController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly MailerInterface $mailer,
        private readonly AvatarService $avatarService,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(FilterFormType::class)->handleRequest($request);

        /** @var Manager|SuperAdministrator $manager */
        $manager = $this->getUserOrThrow();

        /*$employees = $this->userRepository->findForPagination(
            $manager,
            $request->query->getInt('page', 1),
            HasLimit::USER_LIMIT,
            $form->get('keywords')->getData()
        );*/

        $employees = $this->userRepository->getPaginated(
            $manager,
            $request->query->getInt("page", 1),
            HasLimit::USER_LIMIT,
            $form->get("keywords")->getData()
        );

        return $this->render('dashboard/member/index.html.twig', [
            'employees' => $employees,
            'pages' => ceil(count($employees) / HasLimit::USER_LIMIT),
            'form' => $form,
        ]);
    }

    #[Route(path: '/new/{role}', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, string $role): Response
    {
        /** @var SalesPerson|Collaborator|Manager|null $user */
        $user = null;

        switch ($role) {
            case 'collaborator':
                $user = new Collaborator();
                break;
            case 'manager':
                $user = new Manager();
                break;
            case 'salesperson':
                $user = new SalesPerson();
                break;
        }

        /** @var Manager|SuperAdministrator $manager */
        $manager = $this->getUserOrThrow();

        if (1 === $manager->getMembers()->count()) {
            $user->setMember($manager->getMember());
        }

        $form = $this->createForm(AccessFormType::class, $user, ['manager' => $manager])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = md5(random_bytes(16));
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));

            $avatar = $this->avatarService->createAvatar($user->getEmail());
            $user->setAvatar($avatar);
            $user->setLastLoginIp($request->getClientIp());

            $this->em->persist($user);
            $this->em->flush();

            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address(
                        $this->getParameter('website_no_reply_email'),
                        $this->getParameter('website_name'),
                    ))
                    ->to(new Address($user->getEmail(), $user->getFullName()))
                    ->htmlTemplate('mails/welcome.html.twig')
                    ->context(['username' => $user->getUsername(), 'password' => $password])
            );

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Access of %s was created successfully.'),
                    $user->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_member_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/member/new.html.twig', compact('form', 'role'));
    }

    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('EDIT', subject: 'user')]
    public function edit(Request $request, User $user): Response
    {
        /** @var Manager|SuperAdministrator $manager */
        $manager = $this->getUserOrThrow();
        $form = $this->createForm(AccessFormType::class, $user, ['manager' => $manager])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Access of %s was edited successfully.'),
                    $user->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_member_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/member/edit.html.twig', compact('form'));
    }

    #[Route(path: '/{id}/delete', name: 'delete', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('DELETE', subject: 'user')]
    public function delete(Request $request, User $user): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($user);
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Access of %s was deleted successfully.'),
                    $user->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_member_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/member/delete.html.twig', compact('form', 'user'));
    }

    #[Route(path: '/{id}/active', name: 'active', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('ACTIVE', subject: 'user')]
    public function active(Request $request, User $user): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsSuspended(false);
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Access of %s was reactivated successfully.'),
                    $user->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_member_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/member/active.html.twig', compact('form', 'user'));
    }

    #[Route(path: '/{id}/reset', name: 'reset', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('RESET', subject: 'user')]
    public function reset(Request $request, User $user): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = md5(random_bytes(16));
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
            $this->em->flush();

            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address(
                        $this->getParameter('website_no_reply_email'),
                        $this->getParameter('website_name'),
                    ))
                    ->to(new Address($user->getEmail(), $user->getFullName()))
                    ->htmlTemplate('mails/reset.html.twig')
                    ->context(['user' => $user, 'password' => $password])
            );

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('A new password has been generated and sent to %s.'),
                    $user->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_member_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/member/reset.html.twig', compact('form', 'user'));
    }

    #[Route(path: '/{id}/suspend', name: 'suspend', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('SUSPEND', subject: 'user')]
    public function suspend(Request $request, User $user): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsSuspended(true);
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Access of %s was suspended successfully.'),
                    $user->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_member_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/member/suspend.html.twig', compact('form', 'user'));
    }
}
