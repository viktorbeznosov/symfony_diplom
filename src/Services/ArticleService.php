<?php

namespace App\Services;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\ThemeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
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
     * ArticleService constructor.
     */
    public function __construct(
        ThemeDBService $themeDBService,
        ThemeRepository $themeRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager,
        Security $security
    )
    {
        $this->themeDBService = $themeDBService;
        $this->themeRepository = $themeRepository;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->articleRepository = $articleRepository;
    }

    public function insertWords($content, $wordsArray = [])
    {
        $pq = phpQuery::newDocument($content);

        $paragraphs = $pq->find('p');

        while (count($wordsArray) > 0) {
            foreach ($paragraphs as $paragraph) {
                $text = explode(' ', pq($paragraph)->text());
                $position = rand(0, count($text));
                $word = array_pop($wordsArray);
                array_splice($text, $position, 0, $word);
                $str = implode(' ', $text);
                pq($paragraph)->text('');
                pq($paragraph)->text($str);
            }
        }


        return $pq->html();
    }

    public function insertImages($content, $files = [])
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

    public function getArticleData(Request $request)
    {
        $data = $request->request->all();

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

        $files = $request->files->all();
        $content = $this->insertWords($this->themeDBService->getThemeContent($request->request->get('theme_code')), $wordsArray);
        $content = $this->insertImages($content, $files);

        return [
            'data' => $data,
            'files' => $files,
            'content' => $content,
        ];
    }

    public function createArticle(Request $request)
    {
        $articleData = $this->getArticleData($request);
        $article = new Article();
        $article->setUser($this->security->getUser());

        $theme = $this->themeRepository->findOneBy(['code' => $articleData['data']['theme_code']]);
        $article->setTheme($theme);
        $article->setTitle($articleData['data']['articte_title']);
        $article->setDescription($articleData['data']['articte_description']);
        $article->setContent($articleData['content']);
        $article->setMinSize(intval($articleData['data']['min_size']));
        $article->setMaxSize(intval($articleData['data']['max_size']));

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $article;
    }

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
}