<?php

/**
 * Class Es_Framework_Repeater_Field.
 */
class Es_Framework_Repeater_Field extends Es_Framework_Base_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();
		$framework = Es_Framework::get_instance();
		$field_factory = $framework->fields_factory();
		$values = $config['value'];
		unset( $this->_field_config['value'] );
		$input = '';
		$index = 0;

		if ( $fields = $config['fields'] ) {

			if ( ! empty( $values ) ) {
				$values = array_filter( $values );
				foreach ( $values as $key => $value ) {
					$input_item = '';

					if ( ! is_array( $value ) ) continue;

					foreach ( $fields as $name => $field ) {
						if ( empty( $field['name'] ) ) {
							$field['attributes']['name'] = $config['attributes']['name'] . "[{$index}]" . "[$name]";
						} else {
							$field['attributes']['name'] = $field['name'];
						}

						if ( isset( $value[ $name ] ) ) {
							$field['value'] = $value[ $name ];
						}

						$field = $field_factory::get_field_instance( $name, $field );
						$field->_field_config['attributes']['id'] .= "-{$index}";

						if ( ! empty( $field->_field_config['label'] ) ) {
							$field->_field_config['label'] = str_replace( '{#index}', $index + 1, $field->_field_config['label'] );
						}

						$field_input = strtr( $field->get_markup(), array( '{#index}' => $index ) );

						$input_item .= $field_input;
					}
					$index++;
					$input .= sprintf( $config['item_wrapper'], $input_item );
				}
			}

			$input_item = '';

			foreach ( $fields as $name => $field ) {
				if ( empty( $field['name'] ) ) {
					$field['attributes']['name'] = $config['attributes']['name'] . "[{#index}]" . "[$name]";
				} else {
					$field['attributes']['name'] = $field['name'];
				}

				$field['attributes']['disabled'] = 'disabled';

				if ( empty( $field['attributes']['class'] ) ) {
					$field['attributes']['class'] = '';
				}

                $field['attributes']['class'] .= ' js-es-repeater-input';

                foreach ( array( 'before', 'after' ) as $position ) {
	                if ( ! empty( $field['unit_field_' . $position . '_config'] ) ) {
		                $field['unit_field_' . $position . '_config']['attributes']['disabled'] = 'disabled';
		                $field['unit_field_' . $position . '_config']['attributes']['class'] .= ' js-es-repeater-input';
	                }
                }

				$field = $field_factory::get_field_instance( $name, $field );
				$field->_field_config['attributes']['id'] .= '-{#index}';
				$input_item .= $field->get_markup();
			}

			$input_item = sprintf( $config['item_wrapper'], $input_item );
			$input_item = sprintf( $config['to_clone_wrapper'], $input_item );

			$input .= $input_item;
		}

		$content = strtr( $config['repeater_wrapper'], array(
			'{items}' => $input,
		) );

		return strtr( $content, array(
			'{add}' => strtr( $config['add_button'], array(
				'{button_label}' => $config['add_button_label']
			) ),
			'{delete}' => $config['delete_button'],
			'{index}' => $index,
		) );
	}

	/**
	 * @return array
	 */
	public function get_default_config() {

		$default = array(
			'to_clone_wrapper' => "<div class='js-es-to-clone es-hidden'>%s</div>",
			'add_button_label' => __( 'Add new', 'es' ),
			'add_button' => "<button type='button' class='js-es-repeater__add-item es-btn es-btn--add-item es-btn--third es-btn--small'>
								<span class='es-icon es-icon_plus'></span>
								{button_label}
							</button>",
			'delete_button' => "<button type='button' class='js-es-repeater__delete-item es-btn es-btn--delete es-btn--third es-btn--small'>
									<span class='es-icon es-icon_trash'></span>
									" . __( 'Delete' ) . "
								</button>",
			'item_wrapper' => "<div class='js-es-repeater-item es-repeater-item'>%s{delete}</div>",
			'repeater_wrapper' => "<div class='js-es-repeater__wrapper es-repeater__wrapper' data-index='{index}'>
										<div class='js-es-repeater__items'>{items}</div>{add}
									</div>",
			'skeleton' => "{before}<div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>{label}{hidden_input}{caption}{input}{description}</div>{after}",
		);

		return es_parse_args( $default, parent::get_default_config() );
	}
}
