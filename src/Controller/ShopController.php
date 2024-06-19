<?php

namespace App\Controller;

use App\Entity\Shop\Category;
use App\Entity\Shop\Filter;
use App\Entity\Shop\Product;
use App\Entity\Shop\SubCategory;
use App\Entity\Traits\HasLimit;
use App\Form\Shop\FilterFormType;
use App\Repository\Shop\ProductRepository;
use App\Repository\Shop\SubCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/shop', name: 'shop_')]
class ShopController extends AbstractController
{
    #[Route('/{subcategory}/{category}', name: 'index', methods: ['GET'], defaults: ['category' => null, 'subcategory' => null])]
    public function index(
        Request $request,
        ?SubCategory $subcategory,
        ?Category $category,
        SubCategoryRepository $subCategoryRepository,
        ProductRepository $productRepository
    ): Response {
        $min = $productRepository->getMinAmount();
        $max = $productRepository->getMaxAmount();

        $filter = new Filter();
        $filter->min = $min;
        $filter->max = $max;

        $form = $this->createForm(FilterFormType::class, $filter)->handleRequest($request);

        $products = $productRepository->getPaginated(
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 18),
            $request->query->get('sort', 'new-products'),
            $category,
            $filter
        );

        $pages = ceil(count($products) / $request->query->getInt('limit', HasLimit::PRODUCT_LIMIT));

        return $this->render('shop/index.html.twig', [
            'subcategories' => $subCategoryRepository->getSubCategories(),
            'subcategory' => $subcategory,
            'category' => $category,
            'products' => $products,
            'form' => $form,
            'min' => $min,
            'max' => $max,
            'params' => [
                'page' => $request->query->getInt('page', 1),
                'limit' => $request->query->getInt('limit', 18),
                'sort' => $request->query->get('sort', 'new-products'),
            ],
            'pages' => $pages,
            'pageRange' => range(
                max(1, $request->query->getInt('page', 1) - 3),
                min($pages, $request->query->getInt('page', 1) + 3)
            ),
        ]);
    }

    #[Route('/products/{slug}/{cart}', name: 'product', methods: ['GET'], defaults: ['cart' => false], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function product(Product $product, bool $cart): Response
    {
        return $this->render('shop/product.html.twig', compact('product', 'cart'));
    }
}
