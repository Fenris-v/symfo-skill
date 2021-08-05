<?php

namespace App\Homework;

use App\Exception\GenerateException;

class PasteWords
{
    /**
     * Добавляет указанное слово в случайное место текста в указанном количестве
     * @param string $text - текст куда вшить
     * @param string|null $word - слово, которое нужно вшить
     * @param int $wordsCount - количество повторений слова
     * @return string
     * @throws GenerateException
     */
    public function paste(string $text, ?string $word, int $wordsCount = 1): string
    {
        if (!$word) {
            return $text;
        }

        $text = explode(' ', $text);

        $length = count($text) - 1;

        if ($wordsCount >= $length) {
            throw new GenerateException('Задано слишком много повторений слова');
        }

        for ($i = 0; $i < $wordsCount; $i++) {
            $key = rand(1, $length);

            while (stripos($text[$key], $word) === 0) {
                $key = rand(1, $length);
            }

            $text[$key] = "$word $text[$key]";
        }

        return implode(' ', $text);
    }
}
