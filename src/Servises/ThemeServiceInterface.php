<?php
declare(strict_types=1);

namespace App\Servises;

interface ThemeServiceInterface
{
    public function getThemes();

    public function getTheme($code);
}
