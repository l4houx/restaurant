<?php

namespace App\Controller\Dashboard\Main;

use App\Entity\Traits\HasRoles;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MainController extends BaseController
{
    #[Route(path: '/%website_dashboard_path%', name: 'dashboard_main', methods: ['GET'])]
    public function main(AuthorizationCheckerInterface $authChecker): Response
    {
        if ($authChecker->isGranted(HasRoles::TEAM)) {
            return $this->redirectToRoute('admin_dashboard_index');
        } elseif ($authChecker->isGranted(HasRoles::MANAGER)) {
            return $this->redirectToRoute('dashboard_member_access_index');
        } elseif ($authChecker->isGranted(HasRoles::CLIENTACCESS)) {
            return $this->redirectToRoute('dashboard_client_access_index');
        } elseif ($authChecker->isGranted(HasRoles::CLIENTCOMPANY)) {
            return $this->redirectToRoute('dashboard_client_company_index');
        } elseif ($authChecker->isGranted(HasRoles::DEFAULT)) {
            return $this->redirectToRoute('dashboard_account_index');
        }

        return $this->redirectToRoute('signin');
    }
}
