<?php

/**
 * Class Es_Search_Form_Shortcode.
 */
class Es_Property_Field_Shortcode extends Es_Shortcode {

    /**
     * Return search shortcode DOM.
     *
     * @return string|void
     */
    public function get_content() {
        $atts = $this->get_attributes();

        if ( empty( $atts['name'] ) ) return null;

        $value = es_get_the_formatted_field( $atts['name'], $atts['property_id'] );

        return is_scalar( $value ) && strlen( $value ) ? $value : $atts['default'];
    }

    /**
     * Return shortcode default attributes.
     *
     * @return array|void
     */
    public function get_default_attributes() {
        return apply_filters( sprintf( '%s_default_attributes', static::get_shortcode_name() ), array(
            'name' => '',
            'property_id' => get_the_ID(),
	        'default' => null
        ) );
    }

    /**
     * @return Exception|string
     */
    public static function get_shortcode_name() {
        return 'es_property_field';
    }
}
