<?php

/**
 * Class Es_Framework_Checkboxes_Bordered_Field.
 */
class Es_Framework_Checkboxes_Bordered_Field extends Es_Framework_Radio_Bordered_Field {

    function get_input_markup() {
        $markup = parent::get_input_markup();
        $config = $this->get_field_config();

        if ( ! empty( $config['attributes']['multiple'] ) ) {
            $config['attributes']['name'] .= '[]';
        }

        if ( empty( $config['disable_hidden_input'] ) ) {
            $markup = "<input type='hidden' name='" . $config['attributes']['name'] . "' value=''/>" . $markup;
        }

        return $markup;
    }

    /**
     * @return array
     */
    public function get_default_config() {
        $args = array(
            'attributes' => array(
                'multiple' => true,
            ),
        );
        $default = parent::get_default_config();
        return es_parse_args( $args, $default );
    }

    /**
     * @return string
     */
    public function get_multi_field_type() {
        return 'checkbox';
    }
}
