<?php

namespace TidioLiveChat\Logs;

if (!defined('WPINC')) {
    die('File loaded directly. Exiting.');
}


use DateTime;
use TidioLiveChat\Clock\Clock;
use function array_reverse;
use function file_exists;
use function touch;
use function sprintf;
use function error_log;
use function file_get_contents;

final class Logger
{
    /** @var string */
    private $path;
    /** @var Clock */
    private $clock;

    /**
     * @param string $path Debug logs file name
     * @param Clock $clock
     */
    public function __construct($path, $clock)
    {
        $this->path = $path;
        $this->clock = $clock;

        if (!file_exists($this->path)) {
            touch($this->path);
        }
    }

    /**
     * @param string $message
     * @return void
     */
    public function info($message)
    {
        $this->storeLogLine('info', $message);
    }

    /**
     * @param string $message
     * @return void
     */
    public function debug($message)
    {
        $this->storeLogLine('debug', $message);
    }

    /**
     * @param string $message
     * @return void
     */
    public function error($message)
    {
        $this->storeLogLine('error', $message);
    }

    /**
     * @return string
     */
    public function readLog()
    {
        $logContent = file_get_contents($this->path);
        $lines = explode(PHP_EOL, $logContent ?: '');
        $reversedLines = array_reverse($lines);
        $reversedContent = trim(implode(PHP_EOL, $reversedLines));

        return $reversedContent ?: 'Log file is empty';
    }

    /**
     * @return void
     */
    public function clearLog()
    {
        file_put_contents($this->path, '');
    }

    /**
     * @param string $severity
     * @param string $message
     * @return void
     */
    private function storeLogLine($severity, $message)
    {
        $date = $this->clock->getCurrentTimestamp()->format(DateTime::ATOM);
        $formattedError = sprintf('[%s][%s] %s' . PHP_EOL, $date, $severity, $message);
        error_log($formattedError, 3, $this->path);
    }
}
