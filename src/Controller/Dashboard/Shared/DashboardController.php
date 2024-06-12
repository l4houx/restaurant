<?php

namespace App\Controller\Dashboard\Shared;

use App\Entity\User;
use App\Entity\Traits\HasRoles;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/** My Profile Client (User) */
#[IsGranted(HasRoles::DEFAULT)]
class DashboardController extends BaseController
{
    #[Route(path: '/%website_dashboard_path%/user/my-dashboard', name: 'dashboard_user_my_dashboard', methods: ['GET'])]
    public function dashboard(#[CurrentUser] ?User $user): Response
    {
        $user = $this->getUserOrThrow();

        return $this->render('dashboard/shared/dashboard.html.twig', compact('user'));
    }
}
