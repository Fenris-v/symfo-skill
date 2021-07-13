<?php

namespace App\Homework;

use Symfony\Component\Config\Definition\Exception\Exception;

class ArticleContentProvider implements ArticleContentProviderInterface
{
    private array $text = [
        'Accelerare mechanice ducunt ad varius fides. Vae. Cobaltums ire, tanquam flavum demolitione. Superbus nixuss ducunt ad imber. Accelerare foris ducunt ad salvus amor.',
        'Our enlightened anger for core is to know others silently. Confucius says: in space PLURALexperiment earthly faith. When one meets attitude and joy, one is able to receive emptiness.',
        'Shred meatballs immediately, then mix with hollandaise sauce and serve carefully in wok. For a raw sticky tart, add some whipped cream and parsley.',
        'Avast, hunger! All sharks mark gutless, salty wenchs. Ooh, ye swashbuckling pants- set sails for endurance! Rough passions lead to the beauty.',
        'When the vogon goes for starfleet headquarters, all queens open solid, proud spaces. Powerdrain, x-ray vision, and anomaly.',
    ];

    /**
     * ArticleContentProvider constructor.
     */
    public function __construct(private bool $markAsBold)
    {
    }


    /**
     * Генерирует текст статьи
     * @param int $paragraphs - количество абзацев
     * @param string|null $word - слово, которое должно содержаться в тексте
     * @param int $wordsCount - количество повторений этого слова
     * @return string
     */
    public function get(
        int $paragraphs,
        string $word = null,
        int $wordsCount = 0,
    ): string {
        $result = $this->generateText($paragraphs);

        if (!$word || $wordsCount <= 0) {
            return $result;
        }

        $word = $this->wordMarkdown($word);

        return $this->modifyText($result, $word, $wordsCount);
    }

    /**
     * Добавляет стили в соответствие с настройками
     * @param string $word
     * @return string
     */
    private function wordMarkdown(string $word): string
    {
        if ($this->markAsBold) {
            return "**$word**";
        }

        return "*$word*";
    }

    /**
     * Генерирует текст
     * @param int $paragraphs - количество абзацев итогового текста
     * @return string
     */
    private function generateText(int $paragraphs): string
    {
        $result = [];
        for ($i = 0; $i < $paragraphs; $i++) {
            shuffle($this->text);
            $result[] = $this->text[0];
        }

        return implode(PHP_EOL . PHP_EOL, $result);
    }

    /**
     * Добавляет указанное слово в случайное место текста в указанном количестве
     * @param string $text - текст куда вшить
     * @param string $word - слово, которое нужно вшить
     * @param int $wordsCount - количество повторений слова
     * @return string
     */
    private function modifyText(string $text, string $word, int $wordsCount): string
    {
        $text = explode(' ', $text);

        $length = count($text) - 1;

        if ($wordsCount >= $length) {
            throw new Exception('So many words');
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
