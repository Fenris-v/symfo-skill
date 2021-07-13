<?php

namespace App\Service;

use Demontpx\ParsedownBundle\Parsedown;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class MarkdownParser
{
    /**
     * MarkdownParser constructor.
     */
    public function __construct(
        private AdapterInterface $cache,
        private Parsedown $parsedown,
        private LoggerInterface $markdownLogger,
        private bool $debug
    ) {
    }

    public function parse(string $source): string
    {
        if (stripos($source, 'кофе') !== false) {
            $this->markdownLogger->info('Has');
        }

        dump($this->debug);

        if ($this->debug) {
            return $this->parsedown->text($source);
        }

        return $this->cache->get(
            'markdown_' . md5($source),
            function () use ($source) {
                return $this->parsedown->text($source);
            }
        );
    }
}
