<?php

namespace HouseOfAgile\NakaCMSBundle\Helper;

use Psr\Log\LoggerInterface;

trait LoggerCommandTrait
{
    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    protected $isInteractive = false;

    protected $io;
    protected $prefix = '';

    protected $showAllLogs;

    public function __construct($devMode)
    {
        $this->showAllLogs = $devMode;
    }

    protected function setLoaderCommandIo($io): void
    {
        $this->io = $io;
    }

    protected function setInteractive($isInteractive): void
    {
        $this->isInteractive = $isInteractive;
    }

    protected function setPrefix($prefix): void
    {
        $this->prefix = $prefix;
    }

    protected function logWarning($message): void
    {
        $this->logCommand($message, 'warning');
    }

    protected function logInfo($message): void
    {
        $this->logCommand($message, 'info');
    }

    protected function logError($message): void
    {
        $this->logCommand($message, 'error');
    }

    protected function logSuccess($message): void
    {
        $this->logCommand($message, 'success');
    }

    protected function logInteractive($message): void
    {
        if ($this->isInteractive) {
            $this->io->write($message);
        }
    }

    /**
     * we write in console if io is defined
     */
    protected function writeConsole($message, $method = 'writeln'): void
    {
        if ($this->io) {
            $this->io->$method($message);
        }
    }

    protected function logCommand($message, $type = 'debug'): void
    {
        if ($this->prefix) {
            $message = '[#' . trim($this->prefix) . '#] ' . $message;
        }
        switch ($type) {
            case 'debug':
                if ($this->showAllLogs) {
                    $this->writeConsole($message);
                    $this->logger->debug($message);
                }
                break;
            case 'info':
                // if ($this->showAllLogs) {
                    $this->writeConsole('<info>' . $message . '</info>');
                    $this->logger->info($message);
                // }
                break;
            case 'success':
                $this->writeConsole($message, 'success');
                $this->logger->notice($message);
                break;
            case 'warning':
                $this->writeConsole($message, 'warning');
                $this->logger->warning($message);
                break;
            case 'error':
                $this->writeConsole($message, 'error');
                $this->logger->error($message);
                break;
        }
    }
}
