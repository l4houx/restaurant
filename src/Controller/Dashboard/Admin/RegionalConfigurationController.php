<?php

namespace App\Controller\Dashboard\Admin;

use App\DataTransferObject\RegionalConfigurationDto;
use App\Entity\Traits\HasRoles;
use App\Form\Filter\RegionalConfigurationFilter;
use App\Service\RegionalConfigurationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(HasRoles::ADMINAPPLICATION)]
#[Route('/%website_dashboard_path%/admin/manage-settings', name: 'dashboard_admin_setting_')]
class RegionalConfigurationController extends AdminBaseController
{
    public function __construct(
        private readonly RegionalConfigurationService $regionalConfigurationService,
        private readonly RequestStack $requestStack
    ) {
    }

    #[Route(path: '', name: 'regional', methods: ['GET', 'POST'])]
    public function regional(Request $request): Response
    {
        $regionalConfigurationDto = new RegionalConfigurationDto();
        $regionalConfigurationFilter = $this->createForm(RegionalConfigurationFilter::class, $regionalConfigurationDto)->handleRequest($request);
        if ($regionalConfigurationFilter->isSubmitted() && $regionalConfigurationFilter->isValid()) {
            $this->regionalConfigurationService->configure(
                requestStack: $this->requestStack,
                regionalConfigurationDto: $regionalConfigurationDto
            );

            return $this->redirectToRoute('dashboard_admin_setting_regional', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/admin/setting/regional.html.twig', compact('regionalConfigurationFilter'));
    }
}
