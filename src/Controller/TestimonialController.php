<?php

namespace App\Controller;

use App\Entity\Traits\HasLimit;
use App\Service\SettingService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestimonialController extends BaseController
{
    #[Route('/testimonial', name: 'testimonial', methods: ['GET'])]
    public function testimonial(Request $request, SettingService $settingService, PaginatorInterface $paginator): Response
    {
        $keyword = '' == $request->query->get('keyword') ? 'all' : $request->query->get('keyword');

        $testimonials = $paginator->paginate(
            $settingService->getTestimonials(['keyword' => $keyword])->getQuery(), 
            $request->query->getInt('page', 1), 
            HasLimit::TESTIMONIAL_LIMIT, 
            ['wrap-queries' => true]
        );

        return $this->render('testimonial/testimonial-detail.html.twig', compact('testimonials'));
    }
}
