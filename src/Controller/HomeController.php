<?php

namespace App\Controller;

use App\Entity\HomepageHeroSetting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(EntityManagerInterface $em): Response
    {
        return $this->render('home/home.html.twig', [
            //'herosettings' => $em->getRepository(HomepageHeroSetting::class)->find(1),
        ]);
    }
}
