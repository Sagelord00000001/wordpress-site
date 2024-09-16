<?php

use Elementor\Widget_Base;

/**
 * Class Elementor_Es_Base_Widget.
 */
abstract class Elementor_Es_Base_Widget extends Widget_Base {

	public static $CUSTOM_CONTROLS;

    /**
     * @var array
     */
    protected $_custom_controls_keys = array();

    /**
     * Elementor_Es_Base_Widget constructor.
     * @param array $data
     * @param null $args
     * @throws Exception
     */
    public function __construct( $data = array(), $args = null ) {
        parent::__construct( $data, $args );
        $public = ES_PLUGIN_URL . 'public';
        wp_register_script( 'es-elementor-handle', $public . '/js/elementor.min.js', array( 'es-properties', 'elementor-frontend' ), Estatik::get_version(), true );
    }

    /**
     * @param $name
     * @param $config
     */
    public function add_custom_control( $name, $config ) {
        $this->add_control( $name, $config );
        $this->_custom_controls_keys[] = $name;
		static::$CUSTOM_CONTROLS[ $this->get_name() ][$name] = $name;
    }

    /**
     * @return mixed
     */
    public function get_custom_controls_keys() {
        return static::$CUSTOM_CONTROLS[ $this->get_name() ];
    }

    /**
     * @return array
     */
    public function get_script_depends() {
        return [ 'es-elementor-handle' ];
    }

    /**
     * @return array|string[]
     */
    public function get_categories() {
        return array( 'estatik-category' );
    }

	/**
	 * @param $settings
	 *
	 * @return array|mixed
	 */
	public static function prepare_values( $settings ) {
		if ( is_array( $settings ) ) {
			foreach ( $settings as $setting => $value ) {
				if ( is_string( $value ) ) {
					if ( 'yes' == $value ) {
						$settings[ $setting ] = true;
					}

					if ( 'no' == $value ) {
						$settings[ $setting ] = false;
					}

					if ( '' == $value ) {
						unset( $settings[ $setting ] );
					}
				}

				if ( is_array( $value ) ) {
					$settings[ $setting ] = array_filter( $value );
				}
			}
		}

		return $settings;
	}
}