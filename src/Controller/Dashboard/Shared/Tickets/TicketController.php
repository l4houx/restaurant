<?php

namespace App\Controller\Dashboard\Shared\Tickets;

use App\Entity\User;
use App\Form\FilterFormType;
use App\Entity\Tickets\Level;
use App\Entity\Tickets\Status;
use App\Entity\Tickets\Ticket;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Controller\BaseController;
use App\Security\Voter\TicketVoter;
use App\Form\Tickets\TicketFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\Tickets\StatusRepository;
use App\Repository\Tickets\TicketRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
#[IsGranted(HasRoles::DEFAULT)]
class TicketController extends BaseController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly StatusRepository $statusRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly EntityManagerInterface $em,
        private readonly Security $security
    ) {
    }

    #[Route(path: '/my-tickets', name: 'ticket_index', methods: ['GET'])]
    #[IsGranted(TicketVoter::LIST)]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt("limit", HasLimit::TICKET_LIMIT);
        $userId = $this->getUserOrThrow()->getId();
        $findAll = $this->security->isGranted(TicketVoter::LIST_ALL);

        //$tickets = $this->ticketRepository->findForPagination($page, $findAll ? null : $userId);
        $tickets = $this->ticketRepository->getPaginated($page, $limit, $findAll ? null : $userId);
        $pages = ceil(count($tickets) / $request->query->getInt("limit", HasLimit::TICKET_LIMIT));

        return $this->render('dashboard/shared/tickets/index.html.twig', compact("tickets","pages"));
    }

    #[Route(path: '/my-tickets/new', name: 'ticket_new', methods: ['GET', 'POST'])]
    #[IsGranted(TicketVoter::CREATE)]
    public function new(Request $request, #[CurrentUser] ?User $user): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketFormType::class, $ticket)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $status = $this->statusRepository->findOneBy(['name' => 'New']);
                $ticket->setStatus($status);
                $ticket->setUser($user);

                $this->em->persist($ticket);
                $this->em->flush();

                $this->addFlash(
                    'success',
                    sprintf(
                        $this->translator->trans('Ticket %s was created successfully.'),
                        $ticket->getSubject()
                    )
                );

                return $this->redirectToRoute('dashboard_account_response_index', ['id' => $ticket->getId()]);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/shared/tickets/new.html.twig', compact("form","ticket"));
    }

    #[Route(path: '/my-tickets/status/{id}', name: 'ticket_status', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function status(Status $status, #[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            return $this->redirectToRoute('signin', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->security->isGranted(HasRoles::TEAM)) {
            $tickets = $this->ticketRepository->findBy(['status' => $status]);
        } else {
            $ticketsUser = $this->ticketRepository->findBy(['user' => $user->getId(), 'status' => $status]);
            $tickets = array_merge($ticketsUser);
        }

        return $this->render('dashboard/shared/tickets/index.html.twig', compact("tickets"));
    }

    #[Route(path: '/my-tickets/level/{id}', name: 'ticket_level', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function level(Level $level, #[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            return $this->redirectToRoute('signin', [], Response::HTTP_SEE_OTHER);
        }

        $status = $this->statusRepository->findBy(['isClose' => true]);

        if ($this->security->isGranted(HasRoles::TEAM)) {
            $criteriaAdmin = Criteria::create()
                ->andWhere(Criteria::expr()->notIn('status', $status))
                ->andWhere(Criteria::expr()->eq('level', $level))
            ;
            $tickets = $this->ticketRepository->matching($criteriaAdmin);
        } else {
            $criteriaUser = Criteria::create()
                ->orWhere(Criteria::expr()->eq('user', $user))
                ->andWhere(Criteria::expr()->notIn('status', $status))
                ->andWhere(Criteria::expr()->eq('level', $level));
            $tickets = $this->ticketRepository->matching($criteriaUser);
        }

        return $this->render('dashboard/shared/tickets/index.html.twig', compact("tickets"));
    }

    #[Route(path: '/my-tickets/close/{id}', name: 'ticket_close', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function close(Ticket $ticket): Response
    {
        $status = $this->statusRepository->findOneBy(['name' => 'Closed']);
        $ticket->setStatus($status);

        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Ticket was closed successfully.'));

        $this->addFlash(
            'success',
            sprintf(
                $this->translator->trans('Ticket %s was closed successfully.'),
                $ticket->getSubject()
            )
        );

        return $this->redirectToRoute('dashboard_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
