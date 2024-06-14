<?php

namespace App\Controller;

use App\DataTransferObject\SavFormDTO;
use App\Entity\Order\Order;
use App\Entity\Traits\HasRoles;
use App\Form\Order\SavFormType;
use App\Repository\Order\OrderRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(HasRoles::SHOP)]
#[Route('/sav', name: 'sav_')]
class SavController extends BaseController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        $user = $this->getUserOrThrow();

        return $this->render('sav/index.html.twig', [
            'orders' => $orderRepository->findBy(['user' => $user], ['createdAt' => 'desc']),
        ]);
    }

    #[Route('/upload', name: 'upload', methods: ['GET'])]
    public function upload(
        Request $request,
        SluggerInterface $slugger,
        string $publicDirectory,
        string $uploadDirectory
    ): JsonResponse {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $file->move(sprintf('%s/%s', $publicDirectory, $uploadDirectory), $newFilename);

        return $this->json([
            'file' => sprintf('%s/%s', $uploadDirectory, $newFilename),
            'name' => $originalFilename,
        ]);
    }

    #[Route('/{id}/trigger', name: 'trigger', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function trigger(
        Request $request,
        Order $order,
        MailerInterface $mailer,
        TranslatorInterface $translator,
        string $publicDirectory
    ): Response {
        $sav = new SavFormDTO();

        $form = $this->createForm(SavFormType::class, $sav, ['order' => $order])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUserOrThrow();

            $email = (new TemplatedEmail())
                ->from(new Address(
                    $this->getParameter('website_no_reply_email'),
                    $this->getParameter('website_name'),
                ))
                ->to(new Address(
                    $this->getParameter('website_sav'),
                    $this->getParameter('website_name'),
                ))
                ->replyTo(new Address($user->getEmail(), $user->getFullName()))
                ->htmlTemplate('mails/sav.html.twig')
                ->context(['sav' => $sav, 'user' => $user])
            ;

            foreach ($sav->attachments as $attachment) {
                $email->attachFromPath(sprintf('%s/%s', $publicDirectory, $attachment));
            }

            $mailer->send($email);

            $this->addFlash(
                'success',
                $translator->trans('Your after-sales service request has been sent. We will respond as soon as possible.')
            );

            return $this->redirectToRoute('sav_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sav/trigger.html.twig', compact('form', 'order'));
    }
}
