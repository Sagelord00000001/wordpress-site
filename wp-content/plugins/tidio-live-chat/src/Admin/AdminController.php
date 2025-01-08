<?php

namespace TidioLiveChat\Admin;

if (!defined('WPINC')) {
    die('File loaded directly. Exiting.');
}

use TidioLiveChat\Admin\Notice\DismissibleNoticeService;
use TidioLiveChat\TidioSdk\Exception\CannotIntegrateWithProjectException;
use TidioLiveChat\TidioSdk\Exception\CannotRetrieveAccessTokensException;
use TidioLiveChat\TidioSdk\TidioIntegrationService;
use TidioLiveChat\IntegrationState;
use TidioLiveChat\Logs\Logger;
use TidioLiveChat\TidioLiveChat;
use TidioLiveChat\Utils\QueryParameters;
use TidioLiveChat\WooCommerceSdk\WooCommerceIntegrationService;

class AdminController
{
    /**
     * @var TidioIntegrationService
     */
    private $integrationFacade;
    /**
     * @var IntegrationState
     */
    private $integrationState;
    /**
     * @var WooCommerceIntegrationService
     */
    private $wooCommerceIntegrationService;
    /**
     * @var NonceValidator
     */
    private $nonceValidator;
    /**
     * @var DismissibleNoticeService
     */
    private $dismissibleNoticeService;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param TidioIntegrationService $integrationFacade
     * @param IntegrationState $integrationState
     * @param WooCommerceIntegrationService $wooCommerceIntegrationService
     * @param NonceValidator $nonceValidator
     * @param DismissibleNoticeService $dismissibleNoticeService
     */
    public function __construct($integrationFacade, $integrationState, $wooCommerceIntegrationService, $nonceValidator, $dismissibleNoticeService, $logger)
    {
        $this->integrationFacade = $integrationFacade;
        $this->integrationState = $integrationState;
        $this->wooCommerceIntegrationService = $wooCommerceIntegrationService;
        $this->nonceValidator = $nonceValidator;
        $this->dismissibleNoticeService = $dismissibleNoticeService;
        $this->logger = $logger;
    }

    public function handleIntegrateProjectAction()
    {
        if (!$this->isRequestNonceValid(AdminRouting::INTEGRATE_PROJECT_ACTION)) {
            wp_die('', 403);
        }

        $refreshToken = QueryParameters::get('refreshToken');
        try {
            $this->integrationFacade->integrateProject($refreshToken);
        } catch (CannotIntegrateWithProjectException $exception) {
            $errorCode = $exception->getErrorCode();
            $this->redirectToPluginAdminDashboardWithError($errorCode);
        }

        $this->redirectToPluginAdminDashboard();
    }

    public function handleAuthorizeWooCommerceAction()
    {
        if (!$this->isRequestNonceValid(AdminRouting::AUTHORIZE_WOOCOMMERCE_ACTION)) {
            wp_die('', 403);
        }

        try {
            $authUrl = $this->wooCommerceIntegrationService->getAuthUrl();
        } catch (CannotRetrieveAccessTokensException $exception) {
            $this->redirectToPluginAdminDashboardWithError($exception->getErrorCode());
        }

        $this->redirectToUrl($authUrl);
    }

    public function handleIntegrateWooCommerceAction()
    {
        if (!$this->isRequestNonceValid(AdminRouting::INTEGRATE_WOOCOMMERCE_ACTION)) {
            wp_die('', 403);
        }

        if (QueryParameters::has('success') && QueryParameters::get('success') === '1') {
            $this->integrationState->integrateWooCommerce();
        }

        $this->redirectToPluginAdminDashboard();
    }

    public function handleToggleAsyncLoadingAction()
    {
        if (!$this->isRequestNonceValid(AdminRouting::TOGGLE_ASYNC_LOADING_ACTION)) {
            wp_die('', 403);
        }

        $this->integrationState->toggleAsyncLoading();

        $this->redirectToPluginsListDashboard();
    }

    public function handleClearAccountDataAction()
    {
        if (!$this->isRequestNonceValid(AdminRouting::CLEAR_ACCOUNT_DATA_ACTION)) {
            wp_die('', 403);
        }

        $this->integrationState->removeIntegration();
        $this->dismissibleNoticeService->clearDismissedNoticesHistory();

        $this->redirectToPluginsListDashboard();
    }

    public function handleClearLogFile()
    {

        if (!$this->isRequestNonceValid(AdminRouting::CLEAR_LOG_FILE_ACTION)) {
            wp_die('', 403);
        }

        $this->logger->clearLog();

        $this->redirectToPluginSystemInfo();
    }

    /**
     * @param string $nonce
     * @return bool
     */
    private function isRequestNonceValid($nonce)
    {
        return $this->nonceValidator->isRequestNonceValid($nonce);
    }

    private function redirectToPluginsListDashboard()
    {
        $this->redirectToUrl(admin_url('plugins.php'));
    }

    private function redirectToPluginAdminDashboard()
    {
        $this->redirectToAdminPage(TidioLiveChat::TIDIO_PLUGIN_TECHNICAL_NAME);
    }

    private function redirectToPluginSystemInfo()
    {
        $this->redirectToAdminPage(AdminDashboard::TIDIO_SYSTEM_INFO_PAGE);
    }

    /**
     * @param string $name
     * @return void
     */
    private function redirectToAdminPage($name)
    {
        $url = 'admin.php?page=' . $name;
        $this->redirectToUrl(admin_url($url));
    }

    /**
     * @param string $errorCode
     */
    private function redirectToPluginAdminDashboardWithError($errorCode)
    {
        $url = sprintf('admin.php?page=%s&error=%s', TidioLiveChat::TIDIO_PLUGIN_TECHNICAL_NAME, $errorCode);
        $this->redirectToUrl(admin_url($url));
    }

    /**
     * @param string $url
     * @return never-return
     */
    private function redirectToUrl($url)
    {
        wp_redirect($url);
        die();
    }
}
