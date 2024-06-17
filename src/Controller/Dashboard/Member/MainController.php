<?php

namespace App\Controller\Dashboard\Member;

use App\Entity\User;
use App\Entity\Traits\HasRoles;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[IsGranted(HasRoles::MANAGER)]
class MainController extends BaseController
{
    #[Route(path: '/%website_dashboard_path%/member', name: 'dashboard_member_index', methods: ['GET'])]
    public function main(#[CurrentUser] ?User $user): Response
    {
        $user = $this->getUserOrThrow();

        return $this->render('dashboard/member/main.html.twig', compact('user'));
    }
}
