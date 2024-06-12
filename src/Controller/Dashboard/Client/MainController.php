<?php

namespace App\Controller\Dashboard\Client;

use App\Entity\Traits\HasRoles;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(HasRoles::DEFAULT)]
class MainController extends BaseController
{
    #[Route(path: '/%website_dashboard_path%/user', name: 'dashboard_user_index', methods: ['GET'])]
    public function index(): Response
    {
        //return $this->redirectToRoute('dashboard_user_orders');
        return $this->redirectToRoute('dashboard_user_my_dashboard');
    }
}
