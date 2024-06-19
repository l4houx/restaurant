<?php

namespace App\Controller;

use App\Entity\Order\Order;
use App\Entity\User\Manager;
use App\Entity\Traits\HasRoles;
use App\Entity\User\SalesPerson;
use App\Entity\User\SuperAdministrator;
use App\Repository\Order\OrderRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(HasRoles::SHOP)]
#[Route('/order', name: 'order_')]
class OrderController extends BaseController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        $user = $this->getUserOrThrow();

        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findBy(['user' => $user], ['createdAt' => 'desc']),
        ]);
    }

    #[Route('/clients', name: 'clients', methods: ['GET'])]
    public function clients(OrderRepository $orderRepository): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_SALES_PERSON") or is_granted("ROLE_MANAGER")'
        ));

        /** @var SalesPerson|Manager|SuperAdministrator $user */
        $user = $this->getUserOrThrow();

        return $this->render('order/_clients.html.twig', [
            'orders' => $orderRepository->getOrdersByMemberEmployee($user),
        ]);
    }

    #[Route('/{id}/detail', name: 'detail', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('show', subject: 'order')]
    public function detail(Order $order): Response
    {
        return $this->render('order/detail.html.twig', compact('order'));
    }
}
