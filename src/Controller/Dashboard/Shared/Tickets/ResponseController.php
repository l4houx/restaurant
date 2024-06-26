<?php

namespace App\Controller\Dashboard\Shared\Tickets;

use App\Controller\BaseController;
use App\Entity\Tickets\Response as EntityResponse;
use App\Entity\Tickets\Ticket;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Form\Tickets\ResponseFormType;
use App\Repository\Tickets\ResponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
#[IsGranted(HasRoles::DEFAULT)]
class ResponseController extends BaseController
{
    #[Route(path: '/my-responses/{id}', name: 'response_index', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function index(
        Request $request,
        Ticket $ticket,
        TranslatorInterface $translator,
        ResponseRepository $responseRepository,
        #[CurrentUser] ?User $user,
        EntityManagerInterface $em
    ): Response {
        $response = new EntityResponse();
        $form = $this->createForm(ResponseFormType::class, $response)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $response->setTicket($ticket);
                $response->setUser($user);

                $em->persist($response);
                $em->flush();

                $this->addFlash('success', $translator->trans('Answer was created successfully.'));

                return $this->redirectToRoute('dashboard_account_response_index', ['id' => $ticket->getId()]);
            } else {
                $this->addFlash('danger', $translator->trans('The form contains invalid data'));
            }
        }

        $responses = $responseRepository->findBy(['ticket' => $ticket]);

        return $this->render('dashboard/shared/tickets/response.html.twig', compact("form","responses","ticket"));
    }
}
