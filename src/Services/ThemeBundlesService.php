<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ThemeBundlesService implements ThemeServiceInterface
{
    private $themesPath;

    private $nameSpase = 'SymfonyDiplom\\';
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * ThemeBundlesService constructor.
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->themesPath = $this->parameterBag->get('kernel.project_dir') . "/vendor/viktorbeznosov";
    }


    /**
     * @return array
     */
    public function getThemes(): array
    {
        $classes = [];

        if (!is_dir($this->themesPath)) {
            return $classes;
        }

        foreach (scandir($this->themesPath) as $dir) {
            $file = str_replace('Bundle', '', $dir);

            if (class_exists($this->nameSpase . $dir . '\\' . $file)) {
                $fileArr = explode('.', $file);
                $fileName = $fileArr[0];

                $class = $this->nameSpase . $dir . '\\' . $file;
                $classes[] = new $class();
            }
        }

        return $classes;
    }

    /**
     * @param $code
     * @return array|null
     */
    public function getTheme($code)
    {
        $themes = $this->getThemes();

        foreach ($themes as $theme) {
            if (method_exists($theme, 'getCode') && $theme->getCode() == $code) {
                return $theme;
            }
        }

        return null;
    }

    /**
     * @param $code
     * @return |null
     */
    public function getThemeContent($code)
    {
        $theme = $this->getTheme($code);

        if (!empty($theme)) {
            return $theme->getContent();
        }

        return null;
    }
}