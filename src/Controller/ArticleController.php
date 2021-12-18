<?php
declare(strict_types=1);

namespace App\Controller;

use App\Services\ArticleService;
use App\Services\ThemeDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ArticleController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/account/article/create", name="app_article_create")
     */
    public function create(
        Request $request,
        ArticleService $articleService,
        Security $security
    )
    {
        $article = $articleService->createArticle($request);

        return $this->json([
            'article_id' => $article->getId(),
            'content' => $article->getContent()
        ]);
    }
}
