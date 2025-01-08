<?php

namespace TidioLiveChat\Admin;

if (!defined('WPINC')) {
    die('File loaded directly. Exiting.');
}

use TidioLiveChat\IntegrationState;
use TidioLiveChat\TidioLiveChat;
use WP_Admin_Bar;

class AdminDashboard
{
    const TIDIO_ICON_SVG = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIzLjEuMSwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IldhcnN0d2FfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiCgkgdmlld0JveD0iMCAwIDIwIDIwIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAyMCAyMDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPgoJLnN0MHtmaWxsOiNBMEE1QUE7fQo8L3N0eWxlPgo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMiwxOHYtNS45YzAtMi43LDEuNy01LDQuMy01LjdDNy4xLDMuNyw5LjQsMiwxMi4xLDJDMTUuNCwyLDE4LDQuNiwxOCw3Ljl2NS45aC00LjVsMCwwLjEKCWMtMC44LDIuNS0zLDQuMS01LjYsNC4xSDJ6IE02LjEsOC4xTDYuMSw4LjFjLTEuNiwwLjctMi42LDIuMy0yLjYsNC4xdjQuNGg0LjRjMS44LDAsMy4zLTEsNC4xLTIuNmwwLTAuMWwtMC4xLDAKCWMtMy4xLTAuMi01LjYtMi43LTUuNy01LjZsMC0wLjFMNi4xLDguMXogTTEyLjEsMy41Yy0xLjgsMC0zLjMsMS00LjEsMi42bDAsMC4xbDAuMSwwYzMuMiwwLjEsNS43LDIuNyw1LjcsNS45djAuMWwwLjEsMC4xaDIuN1Y3LjkKCUMxNi41LDUuNSwxNC41LDMuNSwxMi4xLDMuNXogTTcuNiw3LjhMNy42LDcuOGMwLDIuNSwyLDQuNSw0LjQsNC41aDAuMWwwLjEtMC4xYzAtMi41LTItNC41LTQuNC00LjVINy43TDcuNiw3Ljh6Ii8+Cjwvc3ZnPgo=';
    const TIDIO_SYSTEM_INFO_PAGE = 'tidio-system-info';
    const WORDPRESS_ADMIN_PAGE_PREFIX = 'admin_page_';

    /** @var IntegrationState */
    private $integrationState;
    /** @var IframeSetup */
    private $iframeSetup;
    /** @var SystemInfoQuery */
    private $systemInfo;

    /**
     * @param IntegrationState $integrationState
     * @param IframeSetup $iframeSetup
     * @param SystemInfoQuery $systemInfo
     */
    public function __construct($integrationState, $iframeSetup, $systemInfo)
    {
        $this->integrationState = $integrationState;
        $this->iframeSetup = $iframeSetup;
        $this->systemInfo = $systemInfo;
    }

    public function load()
    {
        add_action('admin_menu', [$this, 'addAdminMenuLink']);
        add_action('admin_menu', [$this, 'addSystemInfoPage']);
        add_action('admin_bar_menu', [$this, 'addAdminBarItem'], 500);
        add_action('admin_enqueue_scripts', [$this, 'loadStylesAndScripts']);
    }

    public function addAdminMenuLink()
    {
        add_menu_page(
            'Tidio Chat',
            'Tidio Chat',
            'manage_options',
            TidioLiveChat::TIDIO_PLUGIN_TECHNICAL_NAME,
            [$this, 'addAdminPage'],
            self::TIDIO_ICON_SVG,
            '99.12'
        );
    }

    public function addAdminPage()
    {
        $dir = plugin_dir_path(__FILE__);
        if ($this->integrationState->isPluginIntegrated()) {
            $iframeUrl = $this->iframeSetup->prepareIntegrationSuccessIframeUrl();
        } else {
            $iframeUrl = $this->iframeSetup->prepareAuthenticationIframeUrl();
        }

        include_once $dir . '../../views/iframe.php';
    }

    public function addSystemInfoPage()
    {
        add_submenu_page(
            'admin.php',
            'System Info',
            'System Info',
            'manage_options',
            self::TIDIO_SYSTEM_INFO_PAGE,
            [$this, 'createSystemInfoPage']
        );
    }

    public function createSystemInfoPage()
    {
        $dir = plugin_dir_path(__FILE__);
        $systemInfo = $this->systemInfo->getSystemInfo();
        $clearLogFile = AdminRouting::getEndpointForClearLogFileAction();
        include_once $dir . '../../views/system_info.php';
    }

    public function addAdminBarItem(WP_Admin_Bar $adminBar)
    {
        $url = admin_url('admin.php?page=' . TidioLiveChat::TIDIO_PLUGIN_TECHNICAL_NAME);

        $icon = '<span class="custom-icon" style="background-image:url(\'' . self::TIDIO_ICON_SVG . '\');"></span>';

        $adminBar->add_node(
            [
                'id' => 'tidio',
                'title' => $icon . ' <span class="ab-label">Tidio Chat</span>',
                'href' => $url,
            ]
        );
    }

    /**
     * @return void
     */
    public function loadStylesAndScripts($hook)
    {
        $dir = plugin_dir_path(__FILE__) . '../../..';

        wp_enqueue_style('top_bar_css', plugins_url('css/top-bar.css', $dir));

        if (self::WORDPRESS_ADMIN_PAGE_PREFIX . self::TIDIO_SYSTEM_INFO_PAGE === $hook) {
            wp_enqueue_style('system_info_css', plugins_url('css/system-info.css', $dir));
            wp_enqueue_script('system_info_js', plugins_url('js/system-info.js', $dir));
        }
    }
}
