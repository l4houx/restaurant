<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\Authenticator;
use App\Security\EmailVerifier;
use App\Service\AvatarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly EmailVerifier $emailVerifier,
        private readonly AvatarService $avatarService,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserAuthenticatorInterface $userAuthenticator,
        private readonly Authenticator $authenticator,
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/signup', name: 'signup', methods: ['GET', 'POST'])]
    public function signup(Request $request): Response
    {
        if ($this->getUser()) {
            $this->addFlash('danger', $this->translator->trans('Already logged in'));

            return $this->redirectToRoute('home');
        }

        $appErrors = [];

        $user = new User();
        $registrationForm = $this->createForm(RegistrationFormType::class, $user)->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            // encode the plain password
            $user->setPassword(
                $registrationForm->has('plainPassword') ? $this->userPasswordHasher->hashPassword(
                    $user,
                    $registrationForm->get('plainPassword')->getData()
                ) : ''
            );

            $avatar = $this->avatarService->createAvatar($user->getEmail());
            $user->setAvatar($avatar);
            $user->setLastLoginIp($request->getClientIp());
            //$user->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($user);
            $this->em->flush();

            $this->emailVerifier->sendEmailConfirmation('verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address(
                        $this->getParameter('website_no_reply_email'),
                        $this->getParameter('website_name'),
                    ))
                    ->to($user->getEmail())
                    ->subject($this->translator->trans('Please Confirm your Email'))
                    ->htmlTemplate('mails/signup.html.twig')
            );

            return $this->userAuthenticator->authenticateUser(
                $user,
                $this->authenticator,
                $request
            );
        } elseif ($registrationForm->isSubmitted()) {
            /** @var FormError $error */
            foreach ($registrationForm->getErrors() as $error) {
                if (null === $error->getCause()) {
                    $appErrors[] = $error;
                }
            }
        }

        return $this->render('registration/signup.html.twig', [
            'errors' => $appErrors,
            'user' => $user,
            'registrationForm' => $registrationForm,
        ]);
    }

    #[Route(path: '/verify/email', name: 'verify_email', methods: ['GET', 'POST'])]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', $this->translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('home');
        }

        $this->addFlash('success', $this->translator->trans('Your email address has been verified.'));

        return $this->redirectToRoute('home');
    }
}
