<?php

namespace App\Controller;

use App\Entity\Traits\HasLimit;
use App\Form\FilterFormType;
use App\Repository\TestimonialRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestimonialController extends BaseController
{
    #[Route('/testimonial', name: 'testimonial', methods: ['GET'])]
    public function testimonial(Request $request, TestimonialRepository $testimonialRepository): Response
    {
        $form = $this->createForm(FilterFormType::class)->handleRequest($request);

        $testimonials = $testimonialRepository->getPaginated(
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', HasLimit::TESTIMONIAL_LIMIT),
            $form->get('keywords')->getData()
        );

        $pages = ceil(count($testimonials) / $request->query->getInt("limit", HasLimit::TESTIMONIAL_LIMIT));

        return $this->render('testimonial/testimonial-detail.html.twig', compact("form","pages","testimonials"));
    }
}
