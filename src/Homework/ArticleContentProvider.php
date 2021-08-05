<?php

namespace App\Homework;

use App\Exception\GenerateException;

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
    public function __construct(private bool $markAsBold, private PasteWords $pasteWords)
    {
    }

    /**
     * Генерирует текст статьи
     * @param int $paragraphs - количество абзацев
     * @param string|null $word - слово, которое должно содержаться в тексте
     * @param int $wordsCount - количество повторений этого слова
     * @return string
     * @throws GenerateException
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

        return $this->pasteWords->paste($result, $word, $wordsCount);
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
     * @throws GenerateException
     */
    private function generateText(int $paragraphs): string
    {
        if ($paragraphs <= 0) {
            throw new GenerateException('Количество параграфов должно быть больше нуля');
        }

        $result = [];
        for ($i = 0; $i < $paragraphs; $i++) {
            shuffle($this->text);
            $result[] = $this->text[0];
        }

        return implode(PHP_EOL . PHP_EOL, $result);
    }
}
