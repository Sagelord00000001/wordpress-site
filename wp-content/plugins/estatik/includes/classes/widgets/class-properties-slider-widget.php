<?php

/**
 * Class Es_Search_Form_Widget.
 */
class Es_Properties_Slider_Widget extends Es_Properties_Filter_Widget {

    /**
     * Es_Widget_Example constructor.
     */
    public function __construct() {
        parent::__construct( 'es-slider', _x( 'Estatik Slider', 'widget name', 'es' ) );
    }

    /**
     * Render widget content.
     *
     * @param $instance array
     *
     * @return void
     */
    public function render( $instance = array() ) {
        /** @var Es_Properties_Slider_Shortcode $shortcode */
        $shortcode = es_get_shortcode_instance( 'es_properties_slider', $instance );
        echo $shortcode->get_content();
    }

    /**
     * Return default widget data.
     *
     * @return array
     */
    public function get_default_data() {
    	$shortcode = es_get_shortcode_instance( 'es_properties_slider' );
        return array_merge( parent::get_default_data(), $shortcode->get_default_attributes() );
    }

    /**
     * Return widget fields array.
     *
     * @param $instance
     * @return array
     */
    public function get_widget_form( $instance ) {
        $config = array(
            'title' => array(
                'label' => __( 'Title' ),
                'type' => 'text',
            ),

            'layout' => array(
                'label' => __( 'Layout', 'es' ),
                'type' => 'select',
                'options' => array(
                    'horizontal' => _x( 'Horizontal', 'widget layout', 'es' ),
                    'vertical' => _x( 'Vertical', 'widget layout', 'es' ),
                ),
            ),
        );

        $config = array_merge( $config, $this->get_page_access_fields() );

        return array_merge( $config, array(
            'is_arrows_enabled' => array(
                'label' => __( 'Enable arrows', 'es' ),
                'type' => 'switcher',
            ),

            'infinite' => array(
                'label' => __( 'Infinite', 'es' ),
                'type' => 'switcher',
            ),

            'slides_per_serving' => array(
                'label' => __( 'Slides per serving', 'es' ),
                'type' => 'number',
                'attributes' => array(
                    'min' => 0,
                ),
            ),

            'space_between_slides' => array(
                'label' => __( 'Space between slides, px', 'es' ),
                'type' => 'number',
                'attributes' => array(
                    'min' => 0,
                ),
            ),
        ), parent::get_widget_form( $instance ) );
    }
}

new Es_Properties_Slider_Widget();
