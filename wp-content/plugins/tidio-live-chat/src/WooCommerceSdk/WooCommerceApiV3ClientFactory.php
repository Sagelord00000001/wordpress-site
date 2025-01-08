<?php

namespace TidioLiveChat\WooCommerceSdk;

if (!defined('WPINC')) {
    die('File loaded directly. Exiting.');
}

use TidioLiveChat\Http\Client\CurlHttpClient;
use TidioLiveChat\Http\Client\FileGetContentsHttpClient;
use TidioLiveChat\Http\HttpClient;
use TidioLiveChat\Logs\Logger;

class WooCommerceApiV3ClientFactory
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
        $apiUrl = get_rest_url(null, 'wc/v3');

        if (function_exists('curl_version')) {
            return new CurlHttpClient($this->logger, $apiUrl);
        }

        return new FileGetContentsHttpClient($this->logger, $apiUrl);
    }
}
