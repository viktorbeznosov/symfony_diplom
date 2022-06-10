<?php

namespace App\Controller\Api;

use App\Services\ApiArticleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class ArticleController extends AbstractController
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
        return $this->json($articleService->createArticle($request), 200, [], ['groups' => 'article_api']);
    }
}
