<?php

namespace App\Helpers;

use Psr\Log\LoggerInterface;

trait LoggerTrait
{
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @return void
     * @required
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function logInfo(string $message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->info($message, $context);
        }
    }
}
