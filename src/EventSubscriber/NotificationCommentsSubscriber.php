<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use App\Entity\Administrator;
use Symfony\Component\Mime\Email;
use App\Event\CommentCreatedEvent;
use App\Entity\User\SuperAdministrator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationCommentsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TranslatorInterface $translator,
        #[Autowire('%website_no_reply_email%')]
        private readonly string $sender
    ) {
    }

    public function onCommentCreated(CommentCreatedEvent $event): void
    {
        $comment = $event->getComment();

        /** @var Post $post */
        $post = $comment->getPost();

        /** @var SuperAdministrator $author */
        $author = $post->getAuthor();

        /** @var string $emailAddress */
        $emailAddress = $author->getEmail();

        $UrlToPost = $this->urlGenerator->generate('post', [
            'slug' => $post->getSlug(),
            '_fragment' => 'comment_'.$comment->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $subject = $this->translator->trans('Your post received a comment!');
        $body = $this->translator->trans("Your post {$post->getName()} has received a new comment. You can read the comment by following", [
            'name' => $post->getName(),
            'url' => $UrlToPost,
        ]);

        $email = (new Email())
            ->from($this->sender)
            ->to($emailAddress)
            ->subject($subject)
            ->html($body)
        ;

        $this->mailer->send($email);
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CommentCreatedEvent::class => 'onCommentCreated',
        ];
    }
}
