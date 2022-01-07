<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Services\ArticleService;
use App\Services\ThemeDBService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ArticleController extends AbstractController
{

    /**
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param Paginator $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/account/article/articles", name="app_article_articles")
     */
    public function index(
        Request $request,
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator
    )
    {
        $pages = intval($request->get("pages_count")) > 0 ? intval($request->get("pages_count")) : 20;

        $pagination = $paginator->paginate(
            $articleRepository->findAll(),
            $request->query->getInt('page', 1), /* page number */
            $pages /* limit per page */
        );

        return $this->render('admin/articles/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

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
