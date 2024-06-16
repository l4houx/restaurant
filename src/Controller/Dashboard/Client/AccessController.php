<?php

namespace App\Controller\Dashboard\Client;

use App\Entity\User\Manager;
use App\Entity\User\Customer;
use App\Entity\Company\Client;
use App\Service\AvatarService;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Entity\User\SalesPerson;
use App\Controller\BaseController;
use Symfony\Component\Mime\Address;
use App\Entity\User\SuperAdministrator;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Client\Access\AccessFormType;
use App\Form\FilterFormType;
use App\Repository\User\CustomerRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted(HasRoles::CLIENTACCESS)]
#[Route(path: '/%website_dashboard_path%/client/access', name: 'dashboard_client_access_')]
class AccessController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CustomerRepository $customerRepository,
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

        /** @var Manager|SalesPerson|SuperAdministrator $employee */
        $employee = $this->getUserOrThrow();

        /*$customers = $this->customerRepository->findForPagination(
            $employee,
            $request->query->getInt('page', 1),
            HasLimit::USER_LIMIT,
            $form->get('keywords')->getData()
        );*/

        
        $customers = $this->customerRepository->getPaginated(
            $employee,
            $request->query->getInt("page", 1),
            HasLimit::USER_LIMIT,
            $form->get("keywords")->getData()
        );

        //$customers = $this->customerRepository->findAll();

        return $this->render('dashboard/client/index.html.twig', [
            'customers' => $customers,
            'pages' => ceil(count($customers) / HasLimit::USER_LIMIT),
            'form' => $form,
        ]);
    }

    #[Route(path: '/new/{id}', name: 'new', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS], defaults: ['id' => null])]
    #[IsGranted(HasRoles::CLIENTACCESSCREATE)]
    public function new(Request $request, ?Client $client): Response
    {
        $customer = new Customer();

        if (null !== $client) {
            $customer->setClient($client);
        }

        /** @var SalesPerson|Manager|SuperAdministrator $employee */
        $employee = $this->getUserOrThrow();
        $form = $this->createForm(AccessFormType::class, $customer, ['employee' => $employee])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = md5(random_bytes(16));
            $customer->setPassword($this->userPasswordHasher->hashPassword($customer, $password));

            $avatar = $this->avatarService->createAvatar($customer->getEmail());
            $customer->setAvatar($avatar);
            $customer->setLastLoginIp($request->getClientIp());

            $this->em->persist($customer);
            $this->em->flush();

            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address(
                        $this->getParameter('website_no_reply_email'),
                        $this->getParameter('website_name'),
                    ))
                    ->to(new Address($customer->getEmail(), $customer->getFullName()))
                    ->htmlTemplate('mails/welcome.html.twig')
                    ->context(['username' => $customer->getUsername(), 'password' => $password])
            );

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Access of %s was created successfully.'),
                    $customer->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_client_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/client/new-edit.html.twig', compact('form','customer'));
    }

    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('edit', subject: 'customer')]
    public function edit(Customer $customer, Request $request): Response
    {
        /** @var SalesPerson|Manager|SuperAdministrator $employee */
        $employee = $this->getUserOrThrow();
        $form = $this->createForm(AccessFormType::class, $customer, ['employee' => $employee])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($customer);
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Access of %s was edited successfully.'),
                    $customer->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_client_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/client/new-edit.html.twig', compact('form','customer'));
    }

    #[Route(path: '/{id}/delete', name: 'delete', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('delete', subject: 'customer')]
    public function delete(Request $request, Customer $customer): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($customer);
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Access of %s was deleted successfully.'),
                    $customer->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_client_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/client/delete.html.twig', compact('form', 'customer'));
    }

    #[Route(path: '/{id}/active', name: 'active', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('active', subject: 'customer')]
    public function active(Request $request, Customer $customer): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer->setIsSuspended(false);
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Access of %s was reactivated successfully.'),
                    $customer->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_client_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/client/active.html.twig', compact('form', 'customer'));
    }

    #[Route(path: '/{id}/reset', name: 'reset', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('reset', subject: 'customer')]
    public function reset(Request $request, Customer $customer): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = md5(random_bytes(16));
            $customer->setPassword($this->userPasswordHasher->hashPassword($customer, $password));
            $this->em->flush();

            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address(
                        $this->getParameter('website_no_reply_email'),
                        $this->getParameter('website_name'),
                    ))
                    ->to(new Address($customer->getEmail(), $customer->getFullName()))
                    ->htmlTemplate('mails/reset.html.twig')
                    ->context(['customer' => $customer, 'password' => $password])
            );

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('A new password has been generated and sent to %s.'),
                    $customer->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_client_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/client/reset.html.twig', compact('form', 'customer'));
    }

    #[Route(path: '/{id}/suspend', name: 'suspend', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('suspend', subject: 'customer')]
    public function suspend(Request $request, Customer $customer): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer->setIsSuspended(true);
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Access of %s was suspended successfully.'),
                    $customer->getFullName()
                )
            );

            return $this->redirectToRoute('dashboard_client_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/client/suspend.html.twig', compact('form', 'user'));
    }
}
