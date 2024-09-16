<?php

class Es_Restore_Password_Shortcode extends Es_Shortcode {

    /**
     * Return shortcode content.
     *
     * @return string
     */
    public function get_content() {
        $shortcode = es_get_shortcode_instance( 'es_authentication', array(
            'auth_item' => 'reset-form',
        ) );

        return $shortcode->get_content();
    }

    /**
     * Return shortcode name.
     *
     * @return string
     */
    public static function get_shortcode_name() {
        return 'es_reset_pwd';
    }
}