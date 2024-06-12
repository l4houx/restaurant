<?php

namespace App\Controller;

use App\Form\SearchFormType;
use App\Service\SearchService;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', 'search_index', methods: ['GET'])]
    public function index(Request $request, SearchService $searchService): Response
    {
        //$keyword = $request->query->get('keyword');
        $keyword = $request->getPayload()->get('keyword');

        return $this->render('search/dropdown_menu.html.twig', [
            'results' => $searchService->search($keyword),
        ]);
    }

    #[Route('/search-post', 'search_post', methods: ['GET'])]
    public function searchPost(Request $request, PostRepository $postRepository): Response
    {
        $searchForm = $this->createForm(SearchFormType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);

        //$searchKeyword = $request->query->get('keyword');
        $searchKeyword = $request->getPayload()->get('keyword');

        $results = [];

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchKeyword = $searchForm['keyword']->getData();
            $results = $postRepository->searchPost($searchKeyword);
        }

        return $this->render('search/search-post.html.twig', compact('searchKeyword', 'searchForm', 'results'));
    }

    /*
    public function searchPost(Request $request, Meilisearch $meilisearch): Response
    {
        $searchForm = $this->createForm(SearchFormType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);

        $searchKeyword = $request->query->get('keyword');

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchResponse = $meilisearch->rawSearch(Post::class, $searchKeyword, [
                'attributesToHighlight' => ['title', 'content'],
                'highlightPreTag' => '<mark>',
                'highlightPostTag' => '</mark>',
                'attributesToCrop' => ['content'],
                'cropLength' => 20,
            ]);
            $results = $searchResponse['hits'];
        }

        return $this->render('search/search-post.html.twig', [
            'searchKeyword' => $searchKeyword,
            'searchForm' => $searchForm,
            'results' => $results ?? [],
        ]);
    }
    */
}
