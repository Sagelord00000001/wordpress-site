<?php

namespace TidioLiveChat\Admin\Notice;

if (!defined('WPINC')) {
    die('File loaded directly. Exiting.');
}

use TidioLiveChat\Admin\AdminRouting;
use TidioLiveChat\Admin\Notice\Exception\NoticeNameIsNotAllowedException;

class DismissibleNoticeService
{
    const PHP_7_2_REQUIREMENT_NOTICE = 'tidio-php-7-2-requirement-notice';
    const NEW_WOOCOMMERCE_FEATURES_NOTICE = 'tidio-new-woocommerce-features-notice';
    const LYRO_AI_CHATBOT_NOTICE = 'tidio-lyro-ai-chatbot-notice';

    /**
     * @return string[]
     */
    private static function getAllowedNoticeOptions()
    {
        return [
            self::PHP_7_2_REQUIREMENT_NOTICE,
            self::NEW_WOOCOMMERCE_FEATURES_NOTICE,
            self::LYRO_AI_CHATBOT_NOTICE,
        ];
    }

    /**
     * @param string $noticeName
     * @return void
     */
    public function markAsDismissed($noticeName)
    {
        $this->validateNoticeName($noticeName);

        update_option($noticeName, true);
    }

    /**
     * Remember that your script should contain data-dismissible-url="{dismiss_url}"
     *
     * @param string $templatePath
     * @param string $noticeName
     * @return void
     */
    public function displayNotice($templatePath, $noticeName)
    {
        $this->validateNoticeName($noticeName);

        if ($this->wasDismissed($noticeName)) {
            return;
        }

        $script = $this->getNoticeFile($templatePath);

        if (strpos($script, 'data-tidio-dismissible-url="{dismiss_url}"') === false) {
            throw new \RuntimeException('Given script should contains \'data-tidio-dismissible-url={dismiss_url}\' to inject dismissible script.');
        }

        $dismissibleScript = <<<HTML
<script type="text/javascript">
window.onload = function() {
    const successMessage = '[Tidio] Notice has been dismissed successfully.';
    const errorMessageWithStatus = '[Tidio] Could not dismiss tidio notice. Status: ';
    const attributeName = 'tidio-dismissible-url';
    const dataAttributeName = 'data-' + attributeName;
    const dataAttributeNameWithBrackets = '[' + dataAttributeName + ']';
    const noticeClass = '.notice';

    if (window.jQuery) {
        console.log("[Tidio] Dismiss script loaded with jQuery.");

        jQuery(document).ready(function() {
            jQuery(dataAttributeNameWithBrackets).click(function(e) {
                e.preventDefault();
                const noticeParent = jQuery(this).closest(noticeClass);

                noticeParent.fadeOut(200);

                jQuery.ajax({
                    url: jQuery(this).data(attributeName),
                    type: 'post',
                    success: function() {
                        console.log(successMessage);
                    },
                    error: function(e) {
                        console.error(errorMessageWithStatus, e.status);
                    }
                });
            });
        });
    } else {
        console.log("[Tidio] Dismiss script loaded with pure JS. jQuery couldn't be found.");

        const elements = document.querySelectorAll(dataAttributeNameWithBrackets);
        elements.forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();

                const noticeParent = element.closest(noticeClass);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', element.getAttribute(dataAttributeName), true);

                noticeParent.style.display = 'none';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        console.log(successMessage);
                    } else {
                        console.error(errorMessageWithStatus, xhr.status);
                    }
                };

                xhr.onerror = function() {
                    console.error('[Tidio] Could not dismiss notice due to network error.');
                };

                xhr.send();
            });
        });
    }
}</script>
HTML;

        $scriptWithDismissiblePart = strtr($script . $dismissibleScript, ['{dismiss_url}' => $this->buildDismissibleNoticeHref($noticeName)]);

        echo $scriptWithDismissiblePart;
    }

    public function clearDismissedNoticesHistory()
    {
        foreach (self::getAllowedNoticeOptions() as $noticeOption) {
            delete_option($noticeOption);
        }
    }

    /**
     * @param string $noticeName
     * @return string
     */
    private function buildDismissibleNoticeHref($noticeName)
    {
        return AdminRouting::getEndpointForHandleDismissNotice($noticeName);
    }

    /**
     * @param string $name
     * @return bool
     */
    private function wasDismissed($name)
    {
        return (bool) get_option($name);
    }

    /**
     * @param string $noticeName
     * @return void
     * @throws NoticeNameIsNotAllowedException
     */
    private function validateNoticeName($noticeName)
    {
        if ($this->isNoticeNameAllowed($noticeName)) {
            throw NoticeNameIsNotAllowedException::withName($noticeName);
        }
    }

    /**
     * @param string $noticeName
     * @return bool
     */
    private function isNoticeNameAllowed($noticeName)
    {
        return in_array($noticeName, self::getAllowedNoticeOptions(), true) === false;
    }

    private function getNoticeFile($path)
    {
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
