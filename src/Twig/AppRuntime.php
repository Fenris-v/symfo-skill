<?php

namespace App\Twig;

use App\Service\MarkdownParser;
use Carbon\Carbon;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function __construct(private MarkdownParser $markdownParser)
    {
    }

    public function parseMarkdown($content)
    {
        return $this->markdownParser->parse($content);
    }

    public function getDiff($value)
    {
        return Carbon::make($value)->locale('ru')->diffForHumans();
    }
}
