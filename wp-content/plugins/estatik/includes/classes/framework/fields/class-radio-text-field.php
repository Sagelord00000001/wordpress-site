<?php

class Es_Framework_Radio_Text_Field extends Es_Framework_Radio_Boxed_Field {
    /**
     * @return array
     */
    public function get_default_config() {

        $default = array(
            'texts' => array(),
            'wrapper_class' => "es-field es-field__{field_key} es-field--radio-boxed es-box--titled-text es-field--{type}",
        );

        return es_parse_args( $default, parent::get_default_config() );
    }

    /**
     * @return string
     */
    function get_input_markup() {
        $config = $this->get_field_config();
        $input = '';
        $type_class = $config['type'] == 'radio-text' ? 'es-box--titled-text' : null;

        if ( ! empty( $config['options'] ) ) {
            foreach ( $config['options'] as $value => $label ) {
                $input_field = '';
                $field_config = $this->get_multi_field_config( $label, $value );

                unset( $config['before'], $config['after'] );
                $field_config = es_parse_args( $field_config, $config );

                $field = new Es_Framework_Field( $this->_field_key, $field_config );

                $input_field .= $field->get_input_markup();

                if ( ! empty( $config['texts'][ $value ] ) ) {
                    $label = "<div class='es-box__title'>{$label}</div>";
                    $label .= "<p>{$config['texts'][ $value ]}</p>";
                }

                $input .= strtr( $config['item_wrapper'], array(
                    '{input}' => $input_field,
                    '{label}' => $label,
                    '{id}' => $field_config['attributes']['id'],
                    '{type}' => $type_class,
                    '{size}' => $config['size'] ? "es-col-{$config['size']}" : 'es-col',
                    '{item_class}' => $config['item_class'],
                ) );
            }
        }

        return strtr( $config['items_wrapper'], array(
            '{items}' => $input,
        ) );
    }
}
