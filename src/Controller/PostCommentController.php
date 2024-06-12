<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Service\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostCommentController extends AbstractController
{
    #[Route(path: '/post-comment/{slug}/add', name: 'post_comment_add', methods: ['POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    #[IsGranted('IS_AUTHENTICATED')]
    public function postcommentAdd(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Post $post,
        CommentService $commentService,
        CommentRepository $commentRepository,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ): Response {
        $comments = $commentRepository->findRecentComments($post);
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $comment = $form->getData();
                $commentService->createdComment($form, $comment, $post);

                $this->addFlash('success', $translator->trans('Your comment has been sent, thank you. It will be published after validation by a moderator.'));
            } else {
                $this->addFlash('danger', $translator->trans('The form contains invalid data'));
            }

            return $this->redirectToRoute('post', ['slug' => $post->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/post-comment-form-error.html.twig', compact('comments', 'post', 'form'));
    }

    public function form(Post $post): Response
    {
        $form = $this->createForm(CommentFormType::class);

        return $this->render('post/post-comment-form.html.twig', compact('post', 'form'));
    }

    #[Route(path: '/post-comment/comment/{id<[0-9]+>}', name: 'post_comment_delete', methods: ['POST'])]
    #[IsGranted('delete', subject: 'comment')]
    public function postcommentDeleted(
        Request $request,
        CommentService $commentService,
        Comment $comment,
        TranslatorInterface $translator
    ): Response {
        $params = ['slug' => $comment->getPost()->getSlug()];

        if ($this->isCsrfTokenValid('comment_deletion_'.$comment->getId(), $request->getPayload()->get('_token'))) {
            $commentService->deletedComment($comment);
            $this->addFlash('success', $translator->trans('Your comment has been successfully deleted.'));
        }

        return $this->redirectToRoute('post', $params);
    }
}
