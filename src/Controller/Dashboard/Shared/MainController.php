<?php

namespace App\Controller\Dashboard\Shared;

use App\Entity\User;
use App\Entity\Traits\HasRoles;
use App\Controller\BaseController;
use App\Repository\Tickets\LevelRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\Tickets\StatusRepository;
use App\Repository\Tickets\TicketRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/** My Profile User */
#[IsGranted(HasRoles::DEFAULT)]
class MainController extends BaseController
{
    #[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_index', methods: ['GET'])]
    public function main(
        Security $security,
        #[CurrentUser] ?User $user,
        TicketRepository $ticketRepository,
        LevelRepository $levelRepository,
        StatusRepository $statusRepository
    ): Response {
        $user = $this->getUserOrThrow();

        if ($security->isGranted(HasRoles::TEAM)) {
            $tickets = $ticketRepository->findAll();
        } else {
            $ticketsUser = $ticketRepository->findBy(['user' => $user->getId()]);
            $tickets = array_merge($ticketsUser);
        }

        $levels = $levelRepository->findAll();
        $statuses = $statusRepository->findAll();

        $hasActivity = !empty($tickets) || !empty($levels) || !empty($statuses);

        return $this->render('dashboard/shared/main.html.twig', compact("user","tickets","statuses","levels","hasActivity"));
    }
}
