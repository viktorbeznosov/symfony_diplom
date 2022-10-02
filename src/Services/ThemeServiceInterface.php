<?php
declare(strict_types=1);

namespace App\Services;

interface ThemeServiceInterface
{
    public function getThemes();

    public function getTheme($code);

    public function getThemeContent($code);
}
