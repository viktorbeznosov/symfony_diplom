<?php
declare(strict_types=1);

namespace App\Controller;

use App\Servises\ThemeDBService;
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
        ThemeDBService $themeService,
        Security $security
    )
    {
        $article = $themeService->createArticle($request);

        return $this->json([
            'article_id' => $article->getId(),
            'content' => $article->getContent()
        ]);
    }
}
