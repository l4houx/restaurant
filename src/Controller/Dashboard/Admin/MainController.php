<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Traits\HasRoles;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(HasRoles::TEAM)]
class MainController extends AdminBaseController
{
    #[Route(path: '/%website_dashboard_path%/admin', name: 'dashboard_admin_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/admin/index.html.twig');
    }
}
