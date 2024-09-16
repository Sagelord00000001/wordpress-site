<?php

/**
 * Class Es_Shortcode.
 */
abstract class Es_Shortcode {

    /**
     * Shortcode attributes.
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Es_Shortcode constructor.
     *
     * @param array $attributes
     */
    public function __construct( $attributes = array() ) {
        $this->merge_shortcode_attr( $attributes );
    }

    /**
     * @param $attributes
     */
    public function merge_shortcode_attr( $attributes ) {
        $this->_attributes = wp_parse_args( $attributes, $this->get_default_attributes() );
    }

    /**
     * Return shortcode default atts.
     *
     * @return array
     */
    public function get_default_attributes() {
        return array();
    }

    /**
     * Return shortcode name.
     *
     * @return string
     */
    public static function get_shortcode_name() {
        return new Exception( 'Shortcode name is not defined. Override get_shortcode_name method in shortcode child class.' );
    }

    /**
     * Return shortcode content.
     *
     * @param $attributes
     *
     * @return string
     */
    public static function build( $attributes = array() ) {
        $instance = new static( $attributes );

        return $instance->get_content();
    }

    /**
     * Return shortcode content.
     *
     * @return string
     */
    abstract public function get_content();

    /**
     * Return shortcode attributes.
     *
     * @return array
     */
    public function get_attributes() {
        return $this->_attributes;
    }
}
