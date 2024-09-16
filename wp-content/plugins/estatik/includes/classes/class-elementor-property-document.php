<?php

/**
 * Class EstatikProperty.
 */
class Es_Elementor_Property_Document extends \ElementorPro\Modules\ThemeBuilder\Documents\Single_Base {

    /**
     * @return array
     */
    public static function get_properties() {
        $properties = parent::get_properties();

        $properties['location'] = 'single';
        $properties['condition_type'] = 'properties';

        return $properties;
    }

	/**
	 * Return document type.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'single-properties';
	}

	/**
	 * Return document sub type.
	 *
	 * @return string
	 */
	public static function get_sub_type() {
		return 'properties';
	}

	/**
	 * @return mixed
	 */
	protected function get_remote_library_config() {
		$config = parent::get_remote_library_config();

		$config['category'] = 'single properties';

		return $config;
	}

    /**
     * @return string
     */
    protected static function get_site_editor_type() {
        return 'properties';
    }

    /**
     * @return string|void
     */
    public static function get_title() {
        return __( 'Single Property', 'es' );
    }

	/**
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Single Properties', 'es' );
	}
}
