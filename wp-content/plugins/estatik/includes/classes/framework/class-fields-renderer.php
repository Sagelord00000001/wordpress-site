<?php

/**
 * Class Es_Framework_Fields_Renderer.
 */
class Es_Framework_Fields_Renderer {

	/**
	 * @var Es_Framework
	 */
	protected $_framework_instance;

	/**
	 * Fields config array.
	 *
	 * @var array
	 */
	protected $_fields;

	/**
	 * Es_Framework_Fields_Renderer constructor.
	 *
	 * @param $fields_array
	 * @param $framework_instance Es_Framework
	 */
	public function __construct( $fields_array, Es_Framework $framework_instance ) {
		$this->set_fields( $fields_array );
		$this->_framework_instance = $framework_instance;
	}

	/**
	 * Fields array setter.
	 *
	 * @param $fields_array
	 */
	protected function set_fields( $fields_array ) {
		$this->_fields = $fields_array;
	}

	/**
	 * Render fields by provided fields array.
	 *
	 * @return void
	 */
	public function render() {
		if ( is_array( $this->_fields ) && ! empty( $this->_fields ) ) {
			foreach ( $this->_fields as $field_key => $field ) {
				$factory = $this->_framework_instance->fields_factory();
				$field = $factory::get_field_instance( $field_key, $field );

				if ( $field instanceof Es_Framework_Base_Field )
					$field->render();
			}
		}
	}
}
