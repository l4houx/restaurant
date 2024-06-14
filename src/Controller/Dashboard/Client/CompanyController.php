<?php

namespace App\Controller\Dashboard\Client;

use App\Controller\BaseController;
use App\Entity\Company\Client;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Entity\User\Manager;
use App\Form\Client\Company\CompanyFormType;
use App\Form\Client\Company\FilterFormType;
use App\Repository\Company\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(HasRoles::CLIENTCOMPANY)]
#[Route(path: '/%website_dashboard_path%/client/companies', name: 'dashboard_client_company_')]
class CompanyController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ClientRepository $clientRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(FilterFormType::class)->handleRequest($request);

        /** @var Manager $employee */
        $employee = $this->getUser();

        $clients = $this->clientRepository->findForPagination(
            $employee,
            $request->query->getInt('page', 1),
            HasLimit::USER_LIMIT,
            $form->get('keywords')->getData()
        );

        return $this->render('dashboard/client/company/index.html.twig', [
            'clients' => $clients,
            'pages' => ceil(count($clients) / HasLimit::USER_LIMIT),
            'form' => $form,
        ]);
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $client = new Client();

        /** @var Manager $employee */
        $employee = $this->getUser();
        $client->setMember($employee->getMember());

        $form = $this->createForm(CompanyFormType::class, $client, ['employee' => $employee])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($client);
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Client %s was created successfully.'),
                    $client->getName()
                )
            );

            return $this->redirectToRoute('dashboard_client_access_new', [
                'id' => $client->getId(),
            ]);
        }

        return $this->render('dashboard/client/company/new.html.twig', compact('form'));
    }

    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('EDIT', subject: 'client')]
    public function edit(Request $request, Client $client): Response
    {
        /** @var Manager $employee */
        $employee = $this->getUser();
        $client->setMember($employee->getMember());

        $form = $this->createForm(CompanyFormType::class, $client, ['employee' => $employee])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Client %s was edited successfully.'),
                    $client->getName()
                )
            );

            return $this->redirectToRoute('dashboard_client_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/client/company/edit.html.twig', compact('form'));
    }

    #[Route(path: '/{id}/delete', name: 'delete', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('DELETE', subject: 'client')]
    public function delete(Request $request, Client $client): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($client);

            foreach ($client->getCustomers() as $customer) {
                $this->em->remove($customer);
            }

            $this->em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    $this->translator->trans('Client %s has been successfully deleted, along with all associated access.'),
                    $client->getName()
                )
            );

            return $this->redirectToRoute('dashboard_client_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/client/company/delete.html.twig', compact('form', 'client'));
    }
}
