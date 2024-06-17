<?php

namespace App\Controller\Dashboard\Client\Company;

use App\Entity\User;
use App\Entity\Traits\HasRoles;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[IsGranted(HasRoles::CLIENTCOMPANY)]
class MainController extends BaseController
{
    #[Route(path: '/%website_dashboard_path%/client', name: 'dashboard_client_company_index', methods: ['GET'])]

    public function main(#[CurrentUser] ?User $user): Response
    {
        $user = $this->getUserOrThrow();

        return $this->render('dashboard/client/company/main.html.twig', compact('user'));
    }
}
