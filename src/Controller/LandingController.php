<?php
declare(strict_types=1);

namespace App\Controller;

use App\Services\ArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LandingController extends AbstractController
{
    /**
     * @Route("/", name="app_landing_main")
     */
    public function index(): Response
    {
        return $this->render('landing/index.html.twig', [
            'main_page' => true,
        ]);
    }

    /**
     * @Route("/create", name="app_landing_create")
     */
    public function create(
        Request $request,
        ArticleService $articleService
    ): Response
    {
        if ($articleService->getArticleToken()) {
            $article = $articleService->getArticleByToken($request->cookies->get('article_token'));
        }

        return $this->render('landing/create.html.twig', [
            'article' => isset($article) ? $article : null,
            'article_token' => $articleService->getArticleToken()
        ]);
    }
}
