<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('upload_asset', [AppUploadedAsset::class, 'asset']),
        ];
    }


    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'cached_markdown',
                [AppRuntime::class, 'parseMarkdown'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
