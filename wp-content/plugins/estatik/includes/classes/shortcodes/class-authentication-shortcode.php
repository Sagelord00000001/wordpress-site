<?php

/**
 * Class Es_Authentication_Shortcode.
 */
class Es_Authentication_Shortcode extends Es_Shortcode {

    /**
     * @return string
     */
    public function get_content() {
        if ( $auth_item = filter_input( INPUT_GET, 'auth_item' ) ) {
            $this->_attributes['auth_item'] = $auth_item;
        }
        ob_start();
        es_load_template( 'front/shortcodes/authentication/authentication.php', $this->_attributes );
        return ob_get_clean();
    }

    /**
     * Return default attributes.
     *
     * @return array
     */
    public function get_default_attributes() {
        $default = parent::get_default_attributes();

        return es_parse_args( $default, array(
            'auth_item' => 'login-buttons',
            'enable_facebook' => ests( 'is_login_facebook_enabled' ),
            'enable_google' => ests( 'is_login_google_enabled' ),
            'enable_login_form' => ests( 'is_login_form_enabled' ),
            'login_title' => ests( 'login_title' ),
            'login_subtitle' => ests( 'login_subtitle' ),
            'enable_buyers_register' => ests( 'is_buyers_register_enabled' ),
            'buyer_register_title' => ests( 'buyer_register_title' ),
            'buyer_register_subtitle' => ests( 'buyer_register_subtitle' ),
        ) );
    }

    /**
     * Return shortcode name.
     *
     * @return string
     */
    public static function get_shortcode_name() {
        return 'es_authentication';
    }
}
