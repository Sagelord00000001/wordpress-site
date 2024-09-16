<?php

/**
 * Class Es_Profile_Shortcode.
 */
class Es_Profile_Shortcode extends Es_Shortcode {

    /**
     * Return shortcode name.
     *
     * @return string
     */
    public static function get_shortcode_name() {
        return 'es_profile';
    }

    /**
     * @return false|string
     */
    public function get_content() {
        ob_start();

        if ( get_current_user_id() ) {
			$tabs = es_get_profile_tabs();

            es_load_template( 'front/shortcodes/profile/profile.php', array(
                'user_entity' => es_get_user_entity(),
                'tabs' => $tabs
            ) );
        } else {
            $shortcode = es_get_shortcode_instance( 'es_authentication' );
            echo $shortcode->get_content();
        }
        return ob_get_clean();
    }
}
