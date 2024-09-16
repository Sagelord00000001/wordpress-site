<?php

/**
 * Class Es_Framework_Fields_List_Selector.
 *
 * @return void
 */
class Es_Framework_Fields_List_Selector_Field extends Es_Framework_Base_Field {

    function get_input_markup() {
        $config = $this->get_field_config();
        $input = "<ul class='es-items js-es-items'>";

        $hidden_input = es_framework_get_field( $this->_field_key, array(
            'type' => 'hidden',
            'value' => '{value}',
            'attributes' => array(
                'name' => $config['attributes']['name'],
                'multiple' => true,
            ),
        ) );

        if ( ! empty( $config['value'] ) ) {
            foreach ( $config['value'] as $field ) {
                if ( empty( $config['options'][ $field ] ) ) continue;

                $input .= strtr( $config['item_markup'], array(
                    '{field_name}' => $config['options'][ $field ],
                    '{value}' => $field,
                    '{item-id}' => $field,
                    '{hidden}' => str_replace( '{value}', $field, $hidden_input->get_markup() )
                ) );
            }
        }

        $input .= "</ul>";
        if ( ! empty( $config['options'] ) ) {
            $config['attributes']['data-item'] = esc_attr(strtr( $config['item_markup'], array(
                '{hidden}' => $hidden_input->get_markup(),
            ) ) );
            unset( $config['attributes']['name'] );

            $selector = es_framework_get_field( '', array(
                'type' => 'select',
                'options' => $config['options'],
                'attributes' => $config['attributes'],
            ) );

            $input .= "<div class='es-fields-list__selector'>" . $selector->get_markup() . $config['add_button'] . "</div>";
        }

        return $input;
    }

    /**
     * Merge default config.
     *
     * @return array
     */
    public function get_default_config() {
        $parent_config = parent::get_default_config();

        $args = array(
            'item_markup' => "<li class='es-item' data-item-id='{item-id}'><b>{field_name}</b>{hidden}<a href='#' class='js-es-delete-fields-item'><span class='es-icon es-icon_close'></a></li>",
            'add_button' => "<button disabled class='es-btn es-btn--secondary js-es-add-fields-item'>" . __( 'Add', 'es' ) . "</button>",
            'attributes' => array(
                'class' => 'es-field__input js-es-items-selector'
            )
        );
        return es_parse_args( $args, $parent_config );
    }
}
