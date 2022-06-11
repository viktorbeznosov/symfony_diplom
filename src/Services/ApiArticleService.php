<?php

namespace App\Services;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use phpQuery;

class ApiArticleService
{

    /**
     * @var ThemeDBService
     */
    private $themeDBService;
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var WordsService
     */
    private $wordsService;

    /**
     * ApiArticleService constructor.
     * @param ThemeDBService $themeDBService
     * @param ArticleRepository $articleRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ThemeDBService $themeDBService,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager,
        UserService $userService,
        WordsService $wordsService
    )
    {
        $this->themeDBService = $themeDBService;
        $this->articleRepository = $articleRepository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->wordsService = $wordsService;
    }

    public function insertWords($content, $wordsArray = [])
    {
        return $this->wordsService->insertWords($content, $wordsArray);
    }

    public function insertImages($content, $links = [])
    {
        $pq = phpQuery::newDocument($content);

        if (!empty($links)) {
            $images = $pq->find('img');
            foreach ($images as $image) {
                pq($image)->remove();
            }

            $mediaBlocks = $pq->find('.media');

            foreach ($links as $key => $link) {
                pq($mediaBlocks)->eq(rand(1, count($mediaBlocks) - 1))->append('<img class="mr-3" src="' . $link . '" width="250" height="250" alt="">');
            }
        }

        return $pq->html();

    }

    public function getArticleData(Request $request)
    {
        $data = json_decode($request->getContent());

        if (empty($data->theme) || empty($this->themeDBService->getTheme($data->theme))) {
            return [
                'error' => 1,
                'message' => 'Theme not found'
            ];
        }

        $theme = $this->themeDBService->getTheme($data->theme);
        $content = $theme->getContent();

        $wordsArray = [];

        if (!empty($data->keyword)) {
            foreach ($data->keyword as $keyword) {
                $wordsArray[] = $keyword;
            }
        }

        if (!empty($data->words)) {
            foreach ($data->words as $word) {
                for ($i = 0; $i < $word->count; $i++) {
                    $wordsArray[] = $word->word;
                }
            }
        }

        $content = $this->insertWords($content, $wordsArray);
        if (!empty($data->images)) {
            $content = $this->insertImages($content, $data->images);
        }


        return [
            'data' => $data,
            'images' => !empty($data->images) ? $data->images : null,
            'content' => $content,
            'theme' => $theme
        ];
    }

    public function createArticle(Request $request)
    {
        $articleData = $this->getArticleData($request);

        if (!empty($articleData['error'])) {
            return $articleData;
        }

        $data = $articleData['data'];

        $apiToken = $this->getAuthorizationToken($request);
        $user = $this->userService->getUserByApiToken($apiToken);
        $article = new Article();
        $article->setUser($user);
        $article->setTheme($articleData['theme']);
        $article->setTitle($data->title);
        $article->setDescription("Статья " . $articleData['theme']->getTitle());
        $article->setContent($articleData['content']);
        $article->setMinSize(intval($data->size));
        $article->setCreatedAt(Carbon::now());

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return array($article);
    }

    private function getAuthorizationToken(Request $request): string
    {
        $authBearerArray = explode(' ', $request->headers->get('authorization'));

        return $authBearerArray[1];
    }
}