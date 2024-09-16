<?php

/**
 * Class Es_Framework.
 */
class Es_Framework {

	/**
	 * Framework instance.
	 *
	 * @var Es_Framework
	 */
	protected static $_instance;
	protected $_config;

	/**
	 * Estatik constructor.
	 *
	 * @return void
	 */
	public function __construct( $config = array() ) {
		$this->_config = wp_parse_args( $config, array(
			'url' => plugin_dir_url( __FILE__ ),
			'common_url' => defined( 'ES_PLUGIN_URL' ) ? ES_PLUGIN_URL . 'common' : plugin_dir_url( __FILE__ ),
		) );

		$this->load_files();
	}

	/**
	 * @return void
	 */
	public function load_assets() {
		$this->load_styles();
		$this->load_scripts();
	}

	public function load_styles() {
        $url = $this->_config['url'] . 'assets' . DS;
        wp_enqueue_style( 'jqueryui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css', false, null );
        wp_enqueue_style( 'es-framework', $url . 'css' . DS . 'framework.css' );
    }

    public function load_scripts() {
        $url = $this->_config['url'] . 'assets' . DS;
	    $common = $this->_config['common_url'];

	    // Select2
	    wp_register_script( 'es-select2', $common . '/select2/select2.full.min.js', array( 'jquery' ) );
	    wp_enqueue_style( 'es-select2', $common . '/select2/select2.min.css'  );
        wp_enqueue_style( 'es-datetime-picker', $url . 'css/jquery.datetimepicker.min.css', false, null );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'es-datetime-picker', $url . 'js/jquery.datetimepicker.full.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'es-framework', $url . 'js/framework.js', array( 'jquery', 'es-select2', 'jquery-ui-sortable', 'es-datetime-picker' ) );
        wp_localize_script( 'es-framework', 'Es_Framework', array(
            'nonces' => array(
                'attachment_save_caption_nonce' => wp_create_nonce( 'es_framework_attachment_save_caption' ),
            ),
            'tr' => array(
                'add_caption' => __( 'Add caption', 'es' ),
                'failed' => __( 'Failed', 'es' ),
            ),
	        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ) );
    }

	/**
	 * Load framework files.
	 *
	 * @return void
	 */
	protected function load_files() {

		$current_dir = dirname( __FILE__ );

		$files = apply_filters( 'es_framework_files', array(
			$current_dir . DS . 'fields' . DS . 'class-base-field.php',
			$current_dir . DS . 'fields' . DS . 'class-field.php',
			$current_dir . DS . 'fields' . DS . 'class-link-field.php',
			$current_dir . DS . 'fields' . DS . 'class-multi-fields-field.php',
			$current_dir . DS . 'fields' . DS . 'class-date-field.php',
			$current_dir . DS . 'fields' . DS . 'class-date-time-field.php',
			$current_dir . DS . 'fields' . DS . 'class-checkbox-field.php',
			$current_dir . DS . 'fields' . DS . 'class-switcher-field.php',
			$current_dir . DS . 'fields' . DS . 'class-checkboxes-field.php',
			$current_dir . DS . 'fields' . DS . 'class-radio-field.php',
			$current_dir . DS . 'fields' . DS . 'class-phone-field.php',
			$current_dir . DS . 'fields' . DS . 'class-color-field.php',
			$current_dir . DS . 'fields' . DS . 'class-repeater-field.php',
			$current_dir . DS . 'fields' . DS . 'class-select-field.php',
			$current_dir . DS . 'fields' . DS . 'class-media-field.php',
			$current_dir . DS . 'fields' . DS . 'class-images-field.php',
			$current_dir . DS . 'fields' . DS . 'class-iris-color-picker-field.php',
			$current_dir . DS . 'fields' . DS . 'class-incrementer-field.php',
			$current_dir . DS . 'fields' . DS . 'class-radio-boxed-field.php',
			$current_dir . DS . 'fields' . DS . 'class-editor-field.php',
			$current_dir . DS . 'fields' . DS . 'class-checkboxes-boxed-field.php',
			$current_dir . DS . 'fields' . DS . 'class-radio-bordered-field.php',
			$current_dir . DS . 'fields' . DS . 'class-checkboxes-bordered-field.php',
            $current_dir . DS . 'fields' . DS . 'class-radio-image-field.php',
            $current_dir . DS . 'fields' . DS . 'class-radio-text-field.php',
			$current_dir . DS . 'fields' . DS . 'class-textarea-field.php',
			$current_dir . DS . 'fields' . DS . 'class-hidden-field.php',
			$current_dir . DS . 'fields' . DS . 'class-icon-field.php',
			$current_dir . DS . 'fields' . DS . 'class-avatar-field.php',
			$current_dir . DS . 'fields' . DS . 'class-fields-list-selector-field.php',
			$current_dir . DS . 'views' . DS . 'class-view.php',
			$current_dir . DS . 'views' . DS . 'class-tabs-view.php',
			$current_dir . DS . 'class-field-factory.php',
			$current_dir . DS . 'class-view-factory.php',
			$current_dir . DS . 'class-fields-renderer.php',
			$current_dir . DS . 'class-widget-fields-renderer.php',
		) );

		foreach ( $files as $file ) {
			require_once $file;
		}
	}

	/**
	 * Return fields renderer instance.
	 *
	 * @param $fields_config
	 *
	 * @return Es_Framework_Fields_Renderer
	 */
	public function fields_renderer( $fields_config ) {
		return apply_filters( 'es_framework_fields_renderer', new Es_Framework_Fields_Renderer( $fields_config, $this ) );
	}

	/**
	 * Return fields widget renderer instance.
	 *
	 * @param $fields_config
	 * @param $fields_data
	 * @param $widget_instance
	 *
	 * @return Es_Framework_Widget_Fields_Renderer
	 */
	public function widget_fields_renderer( $fields_config, $fields_data, $widget_instance ) {
		return apply_filters( 'es_framework_widget_fields_renderer', new Es_Framework_Widget_Fields_Renderer( $fields_config, $fields_data, $widget_instance, $this ) );
	}

	/**
	 * Return fields factory instance.
	 *
	 * @return Es_Framework_Field_Factory
	 */
	public function fields_factory() {
		return apply_filters( 'es_framework_fields_factory', new Es_Framework_Field_Factory() );
	}

	/**
	 * Views factory.
	 *
	 * @return Es_Framework_View_Factory
	 */
	public function views_factory() {
		return apply_filters( 'es_framework_views_factory', new Es_Framework_View_Factory() );
	}

	/**
	 * Return plugin instance.
	 *
	 * @return Es_Framework
	 */
	public static function get_instance( $config = array() ) {

		if ( ! static::$_instance ) {
			static::$_instance = new static( $config );
		}

		return static::$_instance;
	}

	/**
	 * @return string
	 */
	public static function get_path() {
		return plugin_dir_path( __FILE__ );
	}
}
