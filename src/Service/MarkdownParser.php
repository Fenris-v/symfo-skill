<?php

namespace App\Service;

use Demontpx\ParsedownBundle\Parsedown;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Security\Core\Security;

class MarkdownParser
{
    /**
     * MarkdownParser constructor.
     */
    public function __construct(
        private AdapterInterface $cache,
        private Parsedown $parsedown,
        private LoggerInterface $markdownLogger,
        private bool $debug,
        private Security $security
    ) {
    }

    public function parse(string $source): string
    {
        if (stripos($source, 'длиной') !== false) {
            $this->markdownLogger->info('Has', [
                'user' => $this->security->getUser(),
            ]);
        }

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
