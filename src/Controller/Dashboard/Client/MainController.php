<?php

namespace App\Controller\Dashboard\Client;

use App\Controller\BaseController;
use App\Entity\Traits\HasRoles;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(HasRoles::CLIENTACCESS)]
class MainController extends BaseController
{
    #[Route(path: '/%website_dashboard_path%/client', name: 'dashboard_client_main', methods: ['GET'])]
    public function main(): void
    {
    }
}
