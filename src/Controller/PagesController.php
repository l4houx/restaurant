<?php

namespace App\Controller;

use App\Service\SettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/page')]
class PagesController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '', name: 'pages', methods: ['GET'])]
    public function pages(): Response
    {
        return $this->render('pages/pages.html.twig');
    }

    #[Route(path: '/{slug}', name: 'page', methods: ['GET'])]
    public function page(Request $request, string $slug): Response
    {
        $page = $this->settingService->getPages(['slug' => $slug])->getQuery()->getOneOrNullResult();

        if (!$page) {
            $this->addFlash('danger', $this->translator->trans('The page can not be found'));

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/page-detail.html.twig', compact('page'));
    }

    #[Route(path: '/access-denied', name: 'access_denied', methods: ['GET'])]
    public function accessDenied(): Response
    {
        return $this->render('pages/access-denied.html.twig');
    }
}
