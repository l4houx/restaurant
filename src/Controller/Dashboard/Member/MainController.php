<?php

namespace App\Controller\Dashboard\Member;

use App\Controller\BaseController;
use App\Entity\Traits\HasRoles;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(HasRoles::MANAGER)]
class MainController extends BaseController
{
    #[Route(path: '/%website_dashboard_path%/member', name: 'dashboard_member_main', methods: ['GET'])]
    public function main(): void
    {
    }
}
