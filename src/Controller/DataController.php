<?php

namespace App\Controller;

use App\Data\TransferPointsInterface;
use App\Entity\Data\Account;
use App\Entity\Data\Purchase;
use App\Entity\Data\Transaction;
use App\Entity\Data\Transfer;
use App\Entity\Data\Wallet;
use App\Entity\Traits\HasRoles;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use App\Form\Data\PurchaseFormType;
use App\Form\Data\TransferFormType;
use App\Repository\Data\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/data', name: 'data_')]
class DataController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('data/index.html.twig');
    }

    #[Route('/{id}/export', name: 'export', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function export(Account $account, string $tempDirectory): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($this->translator->trans('Key movements'));
        $sheet->fromArray(
            array_merge(
                [['Date', 'Opération', 'Clés']],
                $account->getTransactions()->map(fn (Transaction $transaction) => [
                    $transaction->getCreatedAt()->format('d/m/Y'),
                    $transaction->getType(),
                    sprintf('%d clés', $transaction->getPoints()),
                ])->toArray()
            )
        );

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($this->translator->trans('Expiration of your keys'));
        $sheet->fromArray(
            array_merge(
                [["Date d'acquisition", "Date d'expiration", 'Clés restantes']],
                $account->getRemainingWallets()->map(fn (Wallet $wallet) => [
                    $wallet->getCreatedAt()->format('d/m/Y'),
                    $wallet->getExpiredAt()->format('d/m/Y'),
                    sprintf('%d clés', $wallet->getBalance()),
                ])->toArray()
            )
        );

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($this->translator->trans('Expired keys'));
        $sheet->fromArray(
            array_merge(
                [["Date d'acquisition", "Date d'expiration", 'Clés restantes']],
                $account->getExpiredWallets()->map(fn (Wallet $wallet) => [
                    $wallet->getCreatedAt()->format('d/m/Y'),
                    $wallet->getExpiredAt()->format('d/m/Y'),
                    sprintf('%d clés', $wallet->getBalance()),
                ])->toArray()
            )
        );

        $filename = sprintf('export_cles_%s.xlsx', (string) Uuid::v4());

        $writer = new Xlsx($spreadsheet);
        $writer->save(sprintf('%s/%s', $tempDirectory, $filename));

        return $this->file(sprintf('%s/%s', $tempDirectory, $filename));
    }

    #[Route('/history/{id}', name: 'history', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function history(Account $account): Response
    {
        return $this->render('data/history.html.twig', compact('account'));
    }

    #[Route('/transfer', name: 'transfer', methods: ['GET', 'POST'])]
    #[IsGranted(HasRoles::DATATRANSFER)]
    public function transfer(Request $request, TransferPointsInterface $transferPoints): Response
    {
        /** @var Manager $manager */
        $manager = $this->getUser();

        $transfer = new Transfer();

        $form = $this->createForm(TransferFormType::class, $transfer, ['manager' => $manager])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transferPoints->execute($transfer);
            $this->em->persist($transfer);
            $this->em->flush();

            $this->addFlash(
                'success',
                $this->translator->trans('Key transfer was completed successfully.')
            );

            return $this->redirectToRoute('data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('data/transfer.html.twig', compact('form'));
    }

    #[Route('/purchase', name: 'purchase', methods: ['GET', 'POST'])]
    #[IsGranted(HasRoles::DATAPURCHASE)]
    public function purchase(Request $request, MailerInterface $mailer): Response
    {
        /** @var Manager $manager */
        $manager = $this->getUser();

        $purchase = new Purchase();

        if (1 === $manager->getMembers()->count()) {
            $purchase->setAccount($manager->getMember()->getAccount());
        }

        $form = $this->createForm(PurchaseFormType::class, $purchase, ['manager' => $manager])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchase->prepare();
            $this->em->persist($purchase);
            $this->em->flush();

            $mailer->send(
                (new TemplatedEmail())
                    ->from(new Address(
                        $this->getParameter('website_no_reply_email'),
                        $this->getParameter('website_name'),
                    ))
                    ->to(new Address(
                        $this->getParameter('website_contact_email'),
                        $this->getParameter('website_name'),
                    ))
                    ->htmlTemplate('mails/data-purchase.html.twig')
                    ->context(['purchase' => $purchase])
            );

            $this->addFlash(
                'success',
                $this->translator->trans('
                    Your key purchase request has been successfully sent. 
                    Upon receipt of payment, the keys will be credited to you.
                ')
            );

            return $this->redirectToRoute('data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('data/purchase.html.twig', compact('form'));
    }

    #[Route('/clients', name: 'clients', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALES_PERSON') or is_granted('ROLE_MANAGER')")]
    public function clients(AccountRepository $accountRepository): Response
    {
        /** @var SalesPerson|Manager $user */
        $user = $this->getUserOrThrow();

        return $this->render('data/clients.html.twig', [
            'accounts' => $accountRepository->getClientsAccountByEmployee($user),
        ]);
    }
}
