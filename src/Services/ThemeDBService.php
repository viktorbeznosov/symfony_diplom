<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\ArticleRepository;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ThemeDBService implements ThemeServiceInterface
{
    /**
     * @var ThemeRepository
     */
    private $themeRepository;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var Security
     */
    private $security;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        ThemeRepository $themeRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->themeRepository = $themeRepository;
        $this->articleRepository = $articleRepository;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function getThemes()
    {
        return $this->themeRepository->findAll();
    }

    public function getTheme($code)
    {
        return $this->themeRepository->findOneBy(['code' => $code]);
    }

    public function getThemeContent($code)
    {
        $article = $this->themeRepository->findOneBy(['code' => $code]);

        return !empty($article) ? $article->getContent() : null;
    }
}
