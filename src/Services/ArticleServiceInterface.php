<?php
declare(strict_types=1);

namespace App\Services;

interface ArticleServiceInterface
{
    public function insertWords($content, $wordsArray = []);

    public function insertImages($content, $files = []);

    public function getArticleData($inputData);

    public function createArticle($inputData);
}