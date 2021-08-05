<?php

namespace App\Homework;

use Faker\Factory;
use Faker\Generator;

class CommentContentProvider implements CommentContentProviderInterface
{
    private Generator $faker;

    public function __construct(private PasteWords $pasteWords)
    {
        $this->faker = Factory::create();
    }

    public function get(string $word = null, int $wordsCount = 0): string
    {
        $text = $this->faker->words($this->faker->numberBetween(10, 100), true);

        return $this->pasteWords->paste($text, $word, $wordsCount);
    }
}
