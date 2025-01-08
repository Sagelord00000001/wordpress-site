<?php

namespace TidioLiveChat\Admin;

if (!defined('WPINC')) {
    die('File loaded directly. Exiting.');
}

use DateTime;
use DateTimeImmutable;
use TidioLiveChat\Clock\Clock;
use TidioLiveChat\Config;
use TidioLiveChat\Logs\Logger;
use function defined;
use function function_exists;
use function phpversion;

class SystemInfoQuery
{
    /** @var Logger */
    public $logger;
    /** @var Clock */
    public $clock;
    /** @var string */
    public $info = '';

    /**
     * @param Logger $logger
     * @param Clock $clock
     */
    public function __construct($logger, $clock)
    {
        $this->logger = $logger;
        $this->clock = $clock;
    }

    /**
     * @return string
     */
    public function getSystemInfo()
    {
        global $wp_version;

        $this->appendSectionStart('System setup');
        $this->appendString('Date', $this->clock->getCurrentTimestamp()->format(DateTime::ATOM));
        $this->appendString('PHP', phpversion());
        $this->appendString('WordPress version', $wp_version);
        $this->appendString('WooCommerce version', defined('WC_VERSION') ? WC_VERSION : '--');
        $this->appendString('WooCommerce REST url', get_rest_url(null, 'wc/v3'));
        $this->appendString('Tidio plugin version', TIDIOCHAT_VERSION);
        $this->appendBool('Curl', function_exists('curl_version'));
        $this->appendSectionEnd();

        $this->appendSectionStart('Tidio plugin config');
        $this->appendString('tidio_api_url', Config::getApiUrl());
        $this->appendString('tidio_panel_url', Config::getPanelUrl());
        $this->appendString('tidio_widget_url', Config::getWidgetUrl());
        $this->appendString('debug_log_path', Config::getDebugLogPath());
        $this->appendSectionEnd();

        $this->appendSectionStart('Logs');
        $this->appendText($this->logger->readLog());
        $this->appendSectionEnd();

        return $this->info;
    }

    /**
     * @param string $param
     * @param bool $message
     * @return void
     */
    private function appendBool($param, $message)
    {
        $this->appendString($param, $message ? "yes" : "no");
    }

    /**
     * @param string $param
     * @param string $message
     * @return void
     */
    private function appendString($param, $message)
    {
        $this->info .= $param . ": " . $message . PHP_EOL;
    }

    /**
     * @param string $name
     * @return void
     */
    private function appendSectionStart($name)
    {
        $this->info .= '=== ' . $name . ' ===' . PHP_EOL;
    }

    /**
     * @return void
     */
    private function appendSectionEnd()
    {
        $this->info .= PHP_EOL;
    }


    /**
     * @param string $text
     * @return void
     */
    private function appendText($text)
    {
        $this->info .= $text . PHP_EOL;
    }
}
