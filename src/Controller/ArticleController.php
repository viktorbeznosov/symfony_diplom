<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Services\ArticleService;
use App\Services\ThemeDBService;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/article/create", name="app_article_create")
     */
    public function create(
        Request $request,
        ArticleService $articleService,
        Security $security
    )
    {
        $article = ($request->cookies->get('article_token')) ? $articleService->getArticleByToken($request->cookies->get('article_token')) : $articleService->createArticle($request->request->all(), $request->files->all());

        $response = new Response();
        $response->headers->setCookie(Cookie::create('article_token', $article->getToken()));
        $content = json_encode([
            'article_id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ]);
        $response->setContent($content);
        $response->send();

    }
}
