<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
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
