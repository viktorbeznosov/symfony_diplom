<?php

namespace App\Services;


use Symfony\Component\HttpFoundation\Request;
use phpQuery;

abstract class AbstractArticleService
{
    abstract public function insertImages($content, $files = []);

    abstract public function getArticleData(Request $request);

    abstract public function createArticle(Request $request);

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
}