<?php

namespace TidioLiveChat\TidioSdk;

if (!defined('WPINC')) {
    die('File loaded directly. Exiting.');
}

use TidioLiveChat\Config;
use TidioLiveChat\Http\Client\CurlHttpClient;
use TidioLiveChat\Http\Client\FileGetContentsHttpClient;
use TidioLiveChat\Http\HttpClient;
use TidioLiveChat\Logs\Logger;

class TidioApiClientFactory
{
    /** @var Logger */
    private $logger;

    /**
     * @param Logger $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return HttpClient
     */
    public function create()
    {
        $apiUrl = Config::getApiUrl();
        if (function_exists('curl_version')) {
            return new CurlHttpClient($this->logger, $apiUrl);
        }

        return new FileGetContentsHttpClient($this->logger, $apiUrl);
    }

    /**
     * @param string $token
     * @return HttpClient
     */
    public function createAuthenticated($token)
    {
        $apiUrl = Config::getApiUrl();
        $authorizationHeader = ['Authorization: Bearer ' . $token];
        if (function_exists('curl_version')) {
            return new CurlHttpClient($this->logger, $apiUrl, $authorizationHeader);
        }

        return new FileGetContentsHttpClient($this->logger, $apiUrl, $authorizationHeader);
    }
}
