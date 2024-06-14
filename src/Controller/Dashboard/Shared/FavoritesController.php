<?php

namespace App\Controller\Dashboard\Shared;

use App\Controller\BaseController;
use App\Entity\Shop\Product;
use App\Entity\Traits\HasRoles;
use App\Repository\Shop\ProductRepository;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/%website_dashboard_path%/account/my-favorites', name: 'dashboard_account_favorites_')]
#[IsGranted(HasRoles::DEFAULT)]
class FavoritesController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProductRepository $productRepository,
        private readonly TranslatorInterface $translator,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $user = $this->getUserOrThrow();

        $rows = $paginator->paginate(
            $this->settingService->getProducts(['addedtofavoritesby' => $this->getUser()])->getQuery(),
            $request->query->getInt('page', 1),
            12,
            ['wrap-queries' => true]
        );

        return $this->render('dashboard/shared/favorites.html.twig', compact('user', 'rows'));
    }

    #[Route(path: '/new/{id}', name: 'new', methods: ['GET'], condition: 'request.isXmlHttpRequest()', requirements: ['id' => Requirement::DIGITS])]
    #[Route(path: '/remove/{id}', name: 'remove', methods: ['POST'], condition: 'request.isXmlHttpRequest()', requirements: ['id' => Requirement::DIGITS])]
    public function newRemove(int $id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->settingService->getProducts(['id' => $id])->getQuery()->getOneOrNullResult();
        if (!$product) {
            return new JsonResponse(['danger' => $this->translator->trans('The product can not be found')]);
        }

        if ($product->isAddedToFavoritesBy($this->getUser())) {
            $this->getUser()->removeFavorite($product);

            $this->em->persist($this->getUser());
            $this->em->flush();

            return new JsonResponse(['danger' => $this->translator->trans('The product has been removed from your favorites')]);
        } else {
            $this->getUser()->addFavorite($product);

            $this->em->persist($this->getUser());
            $this->em->flush();

            return new JsonResponse(['success' => $this->translator->trans('The product has been added to your favorites')]);
        }
    }
}
