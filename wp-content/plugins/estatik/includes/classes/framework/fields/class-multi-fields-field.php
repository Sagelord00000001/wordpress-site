<?php

/**
 * Class Es_Framework_Multi_Fields_Field.
 */
abstract class Es_Framework_Multi_Fields_Field extends Es_Framework_Base_Field {

    /**
     * Return field current values.
     *
     * @return array|mixed
     */
    public function get_values() {
        $config = $this->get_field_config();
	    $config['value'] = is_null( $config['value'] ) ? '' : $config['value'];
        $values = ! is_array( $config['value'] ) ? array( $config['value'] ) : $config['value'];
        $values = array_filter( $values, 'strlen' );
        return empty( $values ) && ! empty( $config['default_value'] ) ? array( $config['default_value'] ) : $values;
    }

    /**
     * Generate multi field config.
     *
     * @param $label
     * @param $value
     * @return array
     */
    public function get_multi_field_config( $label, $value ) {
        $values = $this->get_values();
        $config = $this->get_field_config();

        $field_config = array(
            'type' => $this->get_multi_field_type(),
            'attributes' => array(
                'type' => 'radio',
                'value' => $value,
                'id' => $config['attributes']['id'] . '-' . $value,
            ),
            'label' => $label,
            'value' => in_array( $value, $values ) ? $value : null,
        );

        if ( $field_config['value'] == $field_config['attributes']['value'] && ! is_null( $field_config['value'] ) ) {
            $field_config['attributes']['checked'] = 'checked';
        }

        if ( ! empty( $config['items_attributes'][ $value ] ) ) {
            $field_config = es_parse_args( $config['items_attributes'][ $value ], $field_config );
        }

        $field_config['value'] = $value;

        return $field_config;
    }

    /**
     * Return multi field type.
     *
     * @return string
     */
    abstract public function get_multi_field_type();

    /**
     * @return array|void
     */
    public function get_default_config() {
        $default = parent::get_default_config();
        $config = array(
            'wrapper_class' => 'es-field--multiple-checks'
        );

        return es_parse_args( $config, $default );
    }
}
