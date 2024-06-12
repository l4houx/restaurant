<?php

namespace App\Controller;

use App\Entity\Post;
use App\Service\SettingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostSearchedController extends BaseController
{
    #[Route(path: '/post-searched', name: 'post_searched', methods: ['GET'])]
    public function search(Request $request, SettingService $settingService, TranslatorInterface $translator): Response
    {
        /** @var Post $post */
        $post = $settingService->getBlogPosts([])->getQuery()->getResult();
        if (!$post) {
            $this->addFlash('danger', $translator->trans('The article not be found'));

            return $this->redirectToRoute('posts', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/post-searched.html.twig', [
            'query' => (string) $request->query->get('q', ''),
            'post' => $post
        ]);
    }
}
