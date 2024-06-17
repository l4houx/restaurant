<?php

namespace App\Controller;

use App\Entity\HomepageHeroSetting;
use App\Entity\Post;
use App\Entity\Shop\Category;
use App\Entity\Shop\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function __invoke(EntityManagerInterface $em): Response
    {
        return $this->render('home/home.html.twig', [
            'herosettings' => $em->getRepository(HomepageHeroSetting::class)->find(1),
            'homepageCategories' => $em->getRepository(Category::class)->getLastCategories(6),
            'homepageProducts' => $em->getRepository(Product::class)->getLastProducts(4),
            'homepagePosts' => $em->getRepository(Post::class)->getLastPosts(3),
        ]);
    }
}
