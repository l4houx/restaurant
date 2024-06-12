<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class EmailService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ParameterBagInterface $parameter
    ) {
    }

    public function welcomeEmail(string $emailAddress, array $context = []): TemplatedEmail
    {
        $email = (new TemplatedEmail())
            ->from(new Address(
                $this->parameter->get('website_no_reply_email'),
                $this->parameter->get('website_name'),
            ))
            ->to($emailAddress)
            ->htmlTemplate('mails/welcome.html.twig')
            ->context($context)
        ;

        $this->send($email);

        return $email;
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function send(TemplatedEmail $email): void
    {
        try {
            $this->mailer->send($email);
        } catch (
            TransportExceptionInterface $exception
        ) {
            throw new $exception();
        }
    }
}
