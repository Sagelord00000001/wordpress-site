<?php

/**
 * Class Es_Framework_Base_Field.
 */
abstract class Es_Framework_Base_Field {

	/**
	 * Field simple name for identifier.
	 *
	 * @var string
	 */
	protected $_field_key;

	/**
	 * Fields config array.
	 *
	 * @var array
	 */
	protected $_field_config;

	/**
	 * Es_Framework_Base_Field constructor.
	 *
	 * @param $field_key string
	 * @param $field_config
	 */
	public function __construct( $field_key, $field_config ) {
		$this->_field_key = $field_key;
		$default = $this->get_default_config();
		if ( ! empty( $field_config['attributes']['class'] ) ) {
			$field_config['attributes']['class'] .= ' ' . $default['attributes']['class'];
		}
		$this->_field_config = es_parse_args( $field_config, $default );
	}

	/**
	 * Set field type.
	 *
	 * @param $type
	 *
	 * @return array
	 */
	public function set_type( $type ) {
		$this->_field_config['type'] = $type;

		return $this->_field_config;
	}

	/**
	 * Return hmtl field markup.
	 *
	 * @return string
	 */
	abstract function get_input_markup();

	/**
	 * Return field skeleton.
	 *
	 * @return mixed
	 */
	public function get_skeleton() {
		return $this->_field_config['skeleton'];
	}

	/**
	 * Return field default config.
	 *
	 * @return array
	 */
	public function get_default_config() {

		return array(
			'label' => '',
			'label_wrapper' => "<div class='es-field__label'>%s</div>",
			'description_wrapper' => "<p class='es-field__description'>%s</p>",
			'caption_wrapper' => "<p class='es-field__caption'>%s</p>",
			'wrapper_class' => "",
			'attributes' => array(
				'id' => sprintf( "es-field-%s", $this->_field_key ),
				'name' => $this->_field_key,
				'placeholder' => false,
				'class' => 'es-field__input',
				'multiple' => false,
			),
			'skeleton' => "{before}
                               <div class='js-es-field es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                                   <label for='{id}'>{label}{caption}{input}{description}{reset}</label>
                               </div>
                           {after}",
			'description' => '',
			'caption' => '',
			'default_value' => '',
			'type' => 'text',
			'value' => '',
			'before' => '',
			'after' => '',
			'enable_hidden_input' => false,
			'multiple_index' => false,
			'reset_button' => false,
			'reset_value' => '',
			'reset_label' => __( 'Reset', 'es' ),
		);
	}

	/**
	 * Return skeleton tokens.
	 *
	 * @return array
	 */
	public function get_tokens() {

		$config = $this->get_field_config();
		$hidden = '';

		if ( ! empty( $config['attributes']['readonly'] ) || ! empty( $config['attributes']['disabled'] ) ) {
			$config['wrapper_class'] .= ' es-field--disabled';
		}

		if ( ! empty( $config['enable_hidden_input'] ) ) {
			$hidden = $this->_field_config;
			$hidden['type'] = 'hidden';
			$hidden['attributes']['name'] = $config['attributes']['name'];
			$hidden['value'] = '';
			$hidden['attributes']['value'] = '';

			if ( ! empty( $hidden['attributes']['data-value'] ) ) {
				unset( $hidden['attributes']['data-value'] );
			}

			unset( $hidden['enable_hidden_input'] );

			$hidden = es_framework_get_field_html( $this->_field_key, $hidden );
		}

		return apply_filters( 'es_framework_field_tokens', array(
			'{field_key}' => $this->_field_key,
			'{type}' => $config['type'],
			'{id}' => $config['attributes']['id'],
			'{wrapper_class}' => strtr( $config['wrapper_class'], array(
				'{field_key}' => $this->_field_key,
				'{type}' => $config['type'],
				'{id}' => $config['attributes']['id'],
			) ),
			'{input_class}' => $config['attributes']['class'],
			'{label}' => strtr( $config['label'] ? sprintf( $config['label_wrapper'], $config['label'] ) : '', array(
				'{id}' => $config['attributes']['id'],
			) ),
			'{input}' => $this->get_input_markup(),
			'{description}' => $config['description'] ? sprintf( $config['description_wrapper'], $config['description'] ) : '',
			'{name}' => $config['attributes']['name'],
			'{caption}' => $config['caption'] ? sprintf( $config['caption_wrapper'], $config['caption'] ) : '',
			'{before}' => $config['before'],
			'{after}' => $config['after'],
			'{hidden_input}' => ! empty( $config['enable_hidden_input'] ) ? $hidden : '',
			'{reset}' => ! empty( $config['reset_button'] ) ?
				"<a href='#' class='js-es-reset-value es-reset-value' data-value='" .esc_attr( $config['reset_value'] ) . "'>{$config['reset_label']}</a>" : '',
		), $this );
	}

	/**
	 * Getter for fields config.
	 *
	 * @return array
	 */
	public function get_field_config() {
		return $this->_field_config;
	}

	/**
	 * Return field markup.
	 *
	 * @return string
	 */
	public function get_markup() {
		return strtr( $this->get_skeleton(), $this->get_tokens() );
	}

	/**
	 * Field render handler.
	 *
	 * @return void
	 */
	public function render() {
		echo $this->get_markup();
	}

	/**
	 * Build input html attributes string.
	 *
	 * @return string
	 */
	public function build_attributes_string() {
		$field_config = $this->get_field_config();
		$options = $field_config['attributes'];

		if ( ! empty( $options['multiple'] ) ) {
			if ( strlen( $field_config['multiple_index'] ) ) {
				$options['name'] .= "[{$field_config['multiple_index']}]";
			} else {
				$options['name'] .= '[]';
			}

		}

		return apply_filters( 'es_framework_field_attributes_string', static::build_atts_string( $options ), $this );
	}

	/**
	 * @param $options
	 *
	 * @return string
	 */
	public static function build_atts_string( $options ) {
		$str = '';
		$type = ! empty( $field_config['type'] ) ? $field_config['type'] : '';

		foreach ( $options as $attr_name => $attr_value ) {
			if ( is_string( $attr_value ) || is_numeric( $attr_value ) ) {
				if ( ! $attr_value && $type != 'select' && in_array( $attr_name, array( 'required', 'multiple', 'readonly', 'disabled' ) ) ) continue;

				$str .= $attr_name . '=' . '"' . esc_attr( $attr_value ) . '" ';
			}

			if ( is_array( $attr_value ) ) {
				foreach ( $attr_value as $attr_in_key => $attr_in_value ) {
					$str .= $attr_name . '-' . $attr_in_key . '=' . '"' . esc_attr( $attr_in_value ) . '" ';
				}
			}
		}

		return $str;
	}

}
