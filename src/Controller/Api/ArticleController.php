<?php

namespace App\Controller\Api;

use App\Services\ApiArticleService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends BaseApiController
{
    /**
     * @Route("/api/v1/artice-create", methods={"PUT"}, name="api_article_create")
     * @param Request $request
     * @param ApiArticleService $articleService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createArticle(
        Request $request,
        ApiArticleService $articleService
    )
    {
        if ($this->tokenNotFound($request)) {
            return $this->json(['message' => 'Api token not found']);
        }

        return $this->json($articleService->createArticle($request), 200, [], ['groups' => 'article_api']);
    }
}