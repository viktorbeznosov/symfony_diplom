<?php

namespace App\Services;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\ThemeRepository;
use Carbon\Carbon;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use phpQuery;

/**
 * Class ArticleService
 * @package App\Services
 */
class ArticleService
{
    /**
     * @var ThemeDBService
     */
    private $themeDBService;
    /**
     * @var ThemeRepository
     */
    private $themeRepository;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var WordsService
     */
    private $wordsService;
    /**
     * @var ModuleService
     */
    private $moduleService;

    /**
     * ArticleService constructor.
     */
    public function __construct(
        ThemeDBService $themeDBService,
        ThemeRepository $themeRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager,
        Security $security,
        WordsService $wordsService,
        ModuleService $moduleService
    )
    {
        $this->themeDBService = $themeDBService;
        $this->themeRepository = $themeRepository;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->articleRepository = $articleRepository;
        $this->wordsService = $wordsService;
        $this->moduleService = $moduleService;
    }

    /**
     * @param $content
     * @param array $wordsArray
     * @return string
     */
    public function insertWords($content, $wordsArray = []): string
    {
        return $this->wordsService->insertWords($content, $wordsArray);
    }

    /**
     * @param $content
     * @param array $files
     * @return string
     */
    public function insertImages($content, $files = []): string
    {
        $imdSources = array();

        foreach ($files as $file) {
            $fileName = time() . rand(1,1000) . "." . $file->getClientOriginalExtension();
            $fullFileName = 'uploads/images/article/' . $fileName;

            /** @var UploadedFile $file */
            $file->move('uploads/images/article/', $fileName);
            $imdSources[] = '/uploads/images/article/' . $fileName;
        }

        $pq = phpQuery::newDocument($content);

        if (!empty($files)) {
            $images = $pq->find('img');
            foreach ($images as $image) {
                pq($image)->remove();
            }

            $mediaBlocks = $pq->find('.media');

            foreach ($imdSources as $key => $imdSource) {
                pq($mediaBlocks)->eq(rand(1, count($mediaBlocks) - 1))->append('<img class="mr-3" src="' . $imdSource . '" width="250" height="250" alt="">');
            }
        }

        return $pq->html();
    }

    /**
     * @param $content
     * @param $moduleContents
     * @return string
     */
    public function insertModules($content, $moduleContents): string
    {
        $pq = phpQuery::newDocument($content);
        $contentBlocks = $pq->find(':root')->find('.col-xl-12')->children();
        $contentBlocksCount = $contentBlocks->count();

        foreach ($moduleContents as $content) {
            pq($contentBlocks)->eq(rand(1, $contentBlocksCount - 1))->append($content);
        }

        return $pq->html();
    }

    /**
     * @param array $data
     * @param array $files
     * @return array
     */
    public function getArticleData(array $data, array $files): array
    {
        $wordsArray = [];

        foreach ($data as $key => $item) {
            if (stripos($key,'article_word') !== false) {
                $wordsArray[] = $item;
            }
        }

        if (!empty($data['promoted_words'])) {
            foreach ($data['promoted_words'] as $key => $promotedWord) {
                for ($i = 0; $i < $data['promoted_words_count'][$key]; $i++) {
                    $wordsArray[] = $promotedWord;
                }
            }
        }

        $userModuleContents = $this->moduleService->getUserModuleContents($data);

        $content = $this->insertWords($this->themeDBService->getThemeContent($data['theme_code']), $wordsArray);
        $content = $this->insertImages($content, $files);
        $content = $this->insertModules($content, $userModuleContents);

        return [
            'data' => $data,
            'files' => $files,
            'content' => $content,
        ];
    }

    /**
     * @param array $data
     * @param array $files
     * @return Article
     * @throws \Exception
     */
    public function createArticle(array $data, array $files): Article
    {
        $articleData = $this->getArticleData($data, $files);
        $article = new Article();

        $article->setUser($this->security->getUser());
        $articleToken = $this->security->getUser() ? null : bin2hex(random_bytes(15));
        $article->setToken($articleToken);

        $articleDescription = isset($articleData['data']['articte_description']) ? $articleData['data']['articte_description'] : "";
        $articleMaxSize = isset($articleData['data']['max_size']) ? intval($articleData['data']['max_size']) : 1;

        $theme = $this->themeRepository->findOneBy(['code' => $articleData['data']['theme_code']]);
        $article->setTheme($theme);
        $article->setTitle($articleData['data']['articte_title']);
        $article->setDescription($articleDescription);
        $article->setContent($articleData['content']);
        $article->setMinSize(intval($articleData['data']['min_size']));
        $article->setMaxSize($articleMaxSize);
        $article->setCreatedAt(Carbon::now());

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $article;
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getArticlesHistory(
        Request $request,
        PaginatorInterface $paginator
    )
    {
        $pages = intval($request->get("pages_count")) > 0 ? intval($request->get("pages_count")) : 20;
        $pageNum = $request->request->get('page') ?? 1;

        $pagination = $paginator->paginate(

            $this->articleRepository->findBy(['user' => $this->security->getUser()->getId()]),
            $request->query->getInt('page', $pageNum), /* page number */
            $pages /* limit per page */
        );

        return $pagination;
    }

    /**
     * @return string|null
     */
    public function getArticleToken(): ?string
    {
        return Request::createFromGlobals()->cookies->get('article_token');
    }

    /**
     * @param string $token
     * @return Article
     */
    public function getArticleByToken(string $token): Article
    {
        $article = $this->articleRepository->findOneBy(['token' =>  $token]);

        return $article;
    }

    /**
     * @param User $user
     */
    public function bindDemoArticleToUser(User $user)
    {
        $articleToken = $this->getArticleToken();
        $demoArticle = $this->getArticleByToken($articleToken);
        if ($demoArticle) {
            $demoArticle->setUser($user);
            $demoArticle->setToken(null);
            $this->entityManager->persist($demoArticle);
            $this->entityManager->flush();

            $response = new Response();
            $response->headers->clearCookie('article_token', '/', null);
            $response->sendHeaders();
        }
    }

    /**
     * @param User $user
     * @return mixed[]
     */
    public function getUserArticles(User $user)
    {
        return $this->articleRepository->getUserArticles($user->getId());
    }

    /**
     * @param User $user
     * @return mixed[]
     */
    public function getUserArticlesByLastMonth(User $user)
    {
        return $this->articleRepository->getUserArticlesByPeriod($user->getId(), Carbon::now()->modify('-1 month'), Carbon::now());
    }
}