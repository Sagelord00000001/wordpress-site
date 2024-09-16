<?php

/**
 * Class Es_Framework_Widget_Fields_Renderer.
 */
class Es_Framework_Widget_Fields_Renderer extends Es_Framework_Fields_Renderer {

	/**
	 * Widget instance.
	 *
	 * @var WP_Widget
	 */
	protected $_widget;

	/**
	 * @var array
	 */
	protected $_widget_data;

	/**
	 * Es_Framework_Widget_Fields_Renderer constructor.
	 *
	 * @param $fields_array
	 * @param $widget_data
	 * @param WP_Widget $widget
	 * @param Es_Framework $framework_instance
	 */
	public function __construct( $fields_array, $widget_data, WP_Widget $widget, Es_Framework $framework_instance ) {
		parent::__construct( $fields_array, $framework_instance );

		$this->_widget = $widget;
		$this->_widget_data = $widget_data;
		$this->_fields = $this->prepare_fields( $this->_fields );
	}

	/**
	 * @param $fields
	 *
	 * @return array
	 */
	public function prepare_fields( $fields ) {

		if ( is_array( $fields ) && ! empty( $fields ) ) {
			$w = $this->_widget;
			foreach ( $fields as $field_key => $field_config ) {

				$default_value = isset( $field_config['default_value'] ) ? $field_config['default_value'] : null;

				$fields[ $field_key ]['attributes']['name'] = $w->get_field_name( $field_key );
				$fields[ $field_key ]['attributes']['id'] = $w->get_field_id( $field_key );
				$fields[ $field_key ]['value'] = isset( $this->_widget_data[ $field_key ] ) ? $this->_widget_data[ $field_key ] : $default_value;

				if ( ! empty( $field_config['fields'] ) ) {
					$fields[ $field_key ]['fields'] = $this->prepare_fields( $fields[ $field_key ]['fields'] );
				}
			}
		}

		return $fields;
	}
}
