<?php

namespace App\Services;

use phpQuery;

class WordsService
{
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