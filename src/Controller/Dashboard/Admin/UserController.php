<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\User;
use App\Form\UserFormType;
use App\Form\KeywordFormType;
use App\Service\AvatarService;
use App\Entity\Traits\HasRoles;
use App\Service\SettingService;
use App\Service\SuspendedService;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Exception\PremiumNotBanException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/%website_dashboard_path%/admin/manage-users', name: 'dashboard_admin_user_')]
#[IsGranted(HasRoles::ADMINAPPLICATION)]
class UserController extends AdminBaseController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly MailerInterface $mailer,
        private readonly AvatarService $avatarService,
        private readonly SettingService $settingService,
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        //$page = $request->query->getInt('page', 1);
        //$rows = $this->userRepository->findForPagination($page);

        $form = $this->createForm(KeywordFormType::class)->handleRequest($request);

        $role = '' == $request->query->get('role') ? 'all' : $request->query->get('role');
        $username = '' == $request->query->get('username') ? 'all' : $request->query->get('username');
        $email = '' == $request->query->get('email') ? 'all' : $request->query->get('email');
        $firstname = '' == $request->query->get('firstname') ? 'all' : $request->query->get('firstname');
        $lastname = '' == $request->query->get('lastname') ? 'all' : $request->query->get('lastname');
        $isVerified = '' == $request->query->get('isVerified') ? 'all' : $request->query->get('isVerified');
        $isSuspended = '' == $request->query->get('isSuspended') ? 'all' : $request->query->get('isSuspended');

        $rows = $paginator->paginate($this->settingService->getUsers(['role' => $role, 'username' => $username, 'email' => $email, 'firstname' => $firstname, 'lastname' => $lastname, 'isVerified' => $isVerified, 'isSuspended' => $isSuspended]), $request->query->getInt('page', 1), 10, ['wrap-queries' => true], $form->get("keywords")->getData());

        return $this->render('dashboard/admin/user/index.html.twig', compact('rows', 'form'));
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            /*
            $user->setPassword(
                $form->has('plainPassword') ? $this->userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                ) : ''
            );
            */

            $password = md5(random_bytes(16));
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));

            $avatar = $this->avatarService->createAvatar($user->getEmail());
            $user->setAvatar($avatar);
            $user->setLastLoginIp($request->getClientIp());
            //$user->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($user);
            $this->em->flush();

            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address(
                        $this->getParameter('website_no_reply_email'),
                        $this->getParameter('website_name'),
                    ))
                    ->to(new Address($user->getEmail(), $user->getFullName()))
                    ->htmlTemplate("mails/welcome.html.twig")
                    ->context([
                        "user" => $user,
                        "username" => $user->getUsername(), 
                        "password" => $password
                    ])
            );

            $this->addFlash(
                "success",
                sprintf(
                    $this->translator->trans("Access for %s was created successfully."),
                    $user->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/admin/user/new.html.twig', compact('user', 'form'));
    }

    #[Route(path: '/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('user_deletion_'.$user->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($user);
            $this->em->flush();

            $this->addFlash(
                "success",
                sprintf(
                    $this->translator->trans("%s access has been deleted successfully."),
                    $user->getFullName()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/reset', name: 'reset', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
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
                    ->htmlTemplate("mails/reset.html.twig")
                    ->context(["user" => $user, "password" => $password])
            );

            $this->addFlash(
                "success",
                sprintf(
                    $this->translator->trans("A new password has been generated and sent to %s."),
                    $user->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render("dashboard/admin/user/reset.html.twig", compact('user', 'form'));
    }

    #[Route(path: '/{id}/suspended-enable', name: 'suspended_enable', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function suspendedEnable(Request $request, User $user): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsSuspended(false);
            $this->em->flush();

            $this->addFlash(
                "success",
                sprintf(
                    $this->translator->trans("%s access has been reactivated successfully."),
                    $user->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render("dashboard/admin/user/suspended-enable.html.twig", compact('user', 'form'));
    }

    #[Route(path: '/{id}/suspended-disable', name: 'suspended_disable', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function suspendedDisable(Request $request, User $user): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsSuspended(true);
            $this->em->flush();

            $this->addFlash(
                "success",
                sprintf(
                    $this->translator->trans("%s access has been suspended successfully."),
                    $user->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render("dashboard/admin/user/suspended-disable.html.twig", compact('user', 'form'));
    }

    #[Route(path: '/{id}/verified', name: 'verified', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function verified(User $user): RedirectResponse
    {
        $user->setIsVerified(true);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('The account has been verified successfully.'));

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/more-information', name: 'view', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function view(User $user): Response
    {
        return $this->render('dashboard/admin/user/view.html.twig', compact('user'));
    }

    #[Route(path: '/{id}/change-role', name: 'change_role', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function changeRole(User $user): Response
    {
        $user->setRoles([HasRoles::EDITOR, HasRoles::DEFAULT]);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('The editor role has been successfully added to your user.'));

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/remove-role', name: 'remove_role', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function removeRole(User $user): Response
    {
        $user->setRoles([]);
        $this->em->flush();

        $this->addFlash('danger', $this->translator->trans('The editor role has been successfully remove to your user.'));

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/search', name: 'autocomplete')]
    public function search(Request $request): JsonResponse
    {
        $this->userRepository = $this->em->getRepository(User::class);

        $q = strtolower($request->query->get('q') ?: '');
        if ('moi' === $q) {
            return new JsonResponse([
                [
                    'id' => $this->getUser()->getId(),
                    'username' => $this->getUser()->getUsername(),
                ],
            ]);
        }

        $users = $this->userRepository
            ->createQueryBuilder('u')
            ->select('u.id', 'u.username')
            ->where('LOWER(u.username) LIKE :username')
            ->setParameter('username', "%$q%")
            ->setMaxResults(25)
            ->getQuery()
            ->getResult()
        ;

        return new JsonResponse($users);
    }
}