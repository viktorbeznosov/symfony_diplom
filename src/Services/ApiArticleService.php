<?php
/**
 * Created by PhpStorm.
 * User: viktor
 * Date: 07.06.22
 * Time: 13:52
 */

namespace App\Services;


use App\Entity\Theme;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;

class ApiArticleService implements ArticleServiceInterface
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
     * ApiArticleService constructor.
     * @param ThemeDBService $themeDBService
     * @param ArticleRepository $articleRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ThemeDBService $themeDBService,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->themeDBService = $themeDBService;
        $this->articleRepository = $articleRepository;
        $this->entityManager = $entityManager;
    }

    public function insertWords($content, $wordsArray = [])
    {
        // TODO: Implement insertWords() method.
    }

    public function insertImages($content, $files = [])
    {
        // TODO: Implement insertImages() method.
    }

    public function getArticleData($inputData)
    {
        // TODO: Implement getArticleData() method.
    }

    public function createArticle($inputData)
    {
        // TODO: Implement createArticle() method.
    }

    public function getTheme($code): ?Theme
    {
        return $this->themeDBService->getTheme($code);
    }
}