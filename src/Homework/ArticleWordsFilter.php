<?php

namespace App\Homework;

class ArticleWordsFilter
{
    /**
     * Очищает строку от переданных вхождений
     * @param $string
     * @param array $words
     * @return string
     */
    public function filter($string, $words = []): string
    {
        $reg = '/';
        foreach ($words as $word) {
            $reg .= "(\S*$word\S*\s?)|";
        }
        $reg .= '(\s{2,})/iu';

        return trim(preg_replace($reg, '', $string));
    }
}
