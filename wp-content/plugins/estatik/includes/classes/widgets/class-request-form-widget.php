<?php

/**
 * Class Es_Search_Form_Widget.
 */
class Es_Request_Form_Widget extends Es_Widget {

    /**
     * Es_Widget_Example constructor.
     */
    public function __construct() {
        parent::__construct( 'es-request-form', _x( 'Estatik Request Form', 'widget name', 'es' ) );
    }

    /**
     * Display widget handler.
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        $instance = ! empty( $instance ) ? $instance : $this->get_default_data();
        if ( $this->can_render( $instance ) ) {
            $args = es_parse_args( $args, array(
                'before_widget' => '',
                'after_widget' => '',
                'before_title' => '',
                'after_title' => '',
            ) );

            echo $args['before_widget']; ?>
            <div class="es-widget-wrap">
                <?php $this->render( $instance ); ?>
            </div>
            <?php echo $args['after_widget'];
        }
    }

    /**
     * Render widget on frontend.
     *
     * @param $instance
     *
     * @return void
     */
    public function render( $instance = array() ) {
        $shortcode = es_get_shortcode_instance( 'es_request_form', $instance );
        echo $shortcode->get_content();
    }

    /**
     * Return default widget data.
     *
     * @return array
     */
    public function get_default_data() {
        $shortcode = es_get_shortcode_instance( 'es_request_form' );
        return array_merge( parent::get_default_data(), $shortcode->get_default_attributes() );
    }

    /**
     * Return widget fields array.
     *
     * @param $instance
     * @return array
     */
    public function get_widget_form( $instance ) {
        /** @var Es_Request_Form_Shortcode $shortcode */
        $shortcode = es_get_shortcode_instance( 'es_request_form' );

        return array(
            'title' => array(
                'label' => __( 'Title' ),
                'type'  => 'text',
            ),

            'message' => array(
                'label' => __( 'Message', 'es' ),
                'type' => 'textarea',
            ),

            'disable_name' => array(
                'type' => 'switcher',
                'label' => __( 'Disable name', 'es' ),
            ),

            'disable_tel' => array(
                'type' => 'switcher',
                'label' => __( 'Disable phone', 'es' ),
            ),

            'disable_email' => array(
                'type' => 'switcher',
                'label' => __( 'Disable e-mail', 'es' ),
            ),

            'recipient_type' => array(
                'label' => __( 'Send Message To', 'es' ),
                'type' => 'select',
                'options' => $shortcode::get_send_to_list(),
                'attributes' => array(
                    'class' => 'es-field__input js-es-field__recipient-type',
                ),
            ),

            'custom_email' => array(
                'label' => __( 'Custom emails', 'es' ),
                'type' => 'text',
                'description' => __( 'Comma separated emails', 'es' ),
                'wrapper_class' => $instance['recipient_type'] != -1 ? 'es-hidden' : '',
            ),

            'subject' => array(
                'type' => 'text',
                'label' => __( 'Email subject', 'es' ),
            ),

        );
    }
}

new Es_Request_Form_Widget();
