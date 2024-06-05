<?php

namespace HouseOfAgile\NakaCMSBundle\Monolog\Handler;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Psr\Log\LoggerInterface;

class DeprecationHandler extends StreamHandler
{
    private LoggerInterface $thirdPartyLogger;

    public function __construct(
        $stream,
        LoggerInterface $thirdPartyLogger,
        $level = Level::Debug,
        bool $bubble = true,
    ) {
        parent::__construct($stream, $level, $bubble);
        $this->thirdPartyLogger = $thirdPartyLogger;
    }

    protected function write(LogRecord $record): void
    {
        if ($this->isThirdPartyDeprecation($record->message)) {
            // Log third-party deprecations to a separate logger
            $this->thirdPartyLogger->log($record->level, $record->message, $record->context);
        } else {
            parent::write($record);
        }
    }

    private function isThirdPartyDeprecation(string $message): bool
    {
        // Add logic to detect third-party deprecations, e.g., based on namespaces
        return preg_match('/^(\w+\\\\)+/', $message) === 1;
    }
}
