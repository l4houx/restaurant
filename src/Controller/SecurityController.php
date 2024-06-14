<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RulesFormType;
use App\Repository\RulesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route('/rules', name:'rules', methods: ['GET', 'POST'])]
    public function rulesAction(Request $request, EntityManagerInterface $em, RulesRepository $rulesRepository): Response
    {
        $rules = $rulesRepository->getLastPublishedRules();

        $form = $this->createForm(RulesFormType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            /** @var SubmitButton $acceptButton */
            $acceptButton = $form->get("accept");

            if ($acceptButton->isClicked()) {
                $user->acceptRules($rules);
            } else {
                $user->refuseRules($rules);
            }

            $em->flush();

            if (!$user->hasAcceptedRules($rules)) {
                $this->addFlash("warning", $this->translator->trans("You refused the new settlement."));
                return $this->redirectToRoute("signout");
            }

            $this->addFlash("success", $this->translator->trans("Welcome to ". $this->getParameter('website_name')));

            return $this->redirectToRoute("home");
        }

        return $this->render('security/rules.html.twig', compact('form', 'rules'));
    }

    #[Route(path: '/signin', name: 'signin', methods: ['GET', 'POST'])]
    public function signin(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('danger', $this->translator->trans('Already logged in'));

            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/signin.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/signout', name: 'signout', methods: ['GET', 'POST'])]
    /** @codeCoverageIgnore */
    public function logout(): void
    {
    }
}
