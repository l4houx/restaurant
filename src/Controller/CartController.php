<?php

namespace App\Controller;

use App\Entity\Order\Line;
use App\Entity\Order\Order;
use App\Entity\Shop\Product;
use App\Entity\User\Manager;
use App\Entity\User\Customer;
use App\Entity\Traits\HasRoles;
use App\Entity\User\SalesPerson;
use App\Entity\User\Collaborator;
use App\Form\Order\OrderFormType;
use App\Repository\QuestionRepository;
use App\Entity\User\SuperAdministrator;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Order\OrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(HasRoles::SHOP)]
#[Route('/cart', name: 'cart_')]
class CartController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OrderRepository $orderRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request, WorkflowInterface $orderStateMachine): Response
    {
        $user = $this->getUserOrThrow();

        /** @var ?Order $order */
        $order = $this->orderRepository->findOneBy([
            'state' => 'cart',
            'user' => $user,
        ]);

        if (null === $order) {
            $order = (new Order())->setUser($user);
        }

        $form = $this->createForm(OrderFormType::class, $order)->handleRequest($request);

        if (
            $orderStateMachine->can($order, 'valid_cart')
            && $form->isSubmitted()
            && $form->isValid()
        ) {
            if (!($user instanceof Customer && $user->isManualDelivery())) {
                if ($user instanceof Customer) {
                    $address = $user->getClient()->getMember()->getAddress();
                } else {
                    /** @var SalesPerson|Collaborator|Manager|SuperAdministrator $user */
                    $address = $user->getMember()->getAddress();
                }

                $order->setAddress($address);
            }

            $orderStateMachine->apply($order, 'valid_cart');
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('Your order has been successfully registered.'));

            return $this->redirectToRoute('order_index', [], Response::HTTP_SEE_OTHER);
        }

        $questions = $this->questionRepository->findResults(6);

        return $this->render('cart/index.html.twig', compact('form', 'order', 'questions'));
    }

    #[Route(path: '/add/{slug}', name: 'add', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function add(Product $product): RedirectResponse
    {
        $user = $this->getUserOrThrow();

        /** @var ?Order $order */
        $order = $this->orderRepository->findOneBy([
            'state' => 'cart',
            'user' => $user,
        ]);

        if (null === $order) {
            $order = (new Order())->setUser($user);
            $this->em->persist($order);
        }

        $order->addProduct($product);

        $this->em->flush();

        return $this->redirectToRoute('shop_product', ['slug' => $product->getSlug(), 'cart' => true]);
    }

    #[Route(path: '/{id}/increase-quantity', name: 'increase_quantity', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function increaseQuantity(Line $line): RedirectResponse
    {
        $line->increaseQuantity();

        $this->em->flush();

        return $this->redirectToRoute('cart_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/decrease-quantity', name: 'decrease_quantity', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function decreaseQuantity(Line $line): RedirectResponse
    {
        $line->decreaseQuantity();

        if (0 === $line->getQuantity()) {
            $this->em->remove($line);
        }

        $this->em->flush();

        return $this->redirectToRoute('cart_index', [], Response::HTTP_SEE_OTHER);
    }
}
