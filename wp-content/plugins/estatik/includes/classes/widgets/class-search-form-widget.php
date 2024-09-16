<?php

/**
 * Class Es_Search_Form_Widget.
 */
class Es_Search_Form_Widget extends Es_Widget {

	/**
	 * Es_Widget_Example constructor.
	 */
	public function __construct() {
		parent::__construct( 'es-search-form', _x( 'Estatik Search', 'widget name', 'es' ) );
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
        if ( ! empty( $instance['type'] ) && $instance['type'] != 'main' ) {
            unset( $instance['background'] );
        }
        $shortcode = es_get_shortcode_instance( 'es_search_form', $instance );
        echo $shortcode->get_content();
    }

	/**
	 * Return default widget data.
	 *
	 * @return array
	 */
	public function get_default_data() {
	    $search = es_get_shortcode_instance( 'es_search_form' );
	    $def = $search->get_default_attributes();
	    $def['background'] = '#4d4d4d';
		return array_merge( parent::get_default_data(), $def );
	}

    /**
     * Return widget fields array.
     *
     * @param $instance
     * @return array
     */
	public function get_widget_form( $instance ) {
	    $hide_advanced = $instance['search_type'] == 'advanced' ? 'es-hidden' : '';

		return array_merge( parent::get_widget_form( $instance ), array(
			'title' => array(
				'label' => __( 'Title' ),
				'type' => 'text',
			),

            'search_type' => array(
                'label' => __( 'Select search type', 'es' ),
                'type' => 'radio-bordered',
                'options' => array(
                    'main' => _x( 'Main', 'search widget', 'es' ),
                    'simple' => _x( 'Simple', 'search widget', 'es' ),
                    'advanced' => _x( 'Advanced', 'search widget', 'es' ),
                ),
                'attributes' => array(
                    'class' => 'es-field__input js-es-search-type'
                ),
            ),

			'search_page_id' => array(
				'label' => __( 'Search results page', 'es' ),
				'type' => 'select',
				'options_callback' => 'es_get_pages',
				'attributes' => array(
					'placeholder' => __( 'Choose page', 'es' )
				),
			),

            'background' => array(
                'label' => __( 'Background color', 'es' ),
                'type' => 'color',
                'wrapper_class' => $instance['search_type'] == 'main' ? 'es-field--color--break-label' : 'es-hidden',
            ),

            'enable_saved_search' => array(
                'type' => 'switcher',
                'label' => __( 'Enable saved search', 'es' ),
                'description' => __( 'Only for Simple or Advanced search layout', 'es' ),
            ),

            'is_address_search_enabled' => array(
                'before' => '<hr/><h4>' . __( 'Search by location', 'es' ) . '</h4>',
                'type' => 'switcher',
                'label' => __( 'Enable search by address, city, ZIP', 'es' ),
                'attributes' => array(
                    'data-toggle-container' => '#es-address-search-options--' . $this->id
                ),
            ),

            'is_main_filter_enabled' => array(
                'before' => '<div class="js-es-search-fields ' . $hide_advanced . '"><hr/><h4>' . __( 'Main filters', 'es' ) . '</h4>',
                'type' => 'switcher',
                'label' => __( 'Enable main filters', 'es' ),
                'attributes' => array(
                    'data-toggle-container' => '#es-main-filter-options--' . $this->id
                ),
            ),

            'main_fields' => array(
                'before' => "<div id='es-main-filter-options--{$this->id}'>",
                'type' => 'fields-list-selector',
                'options' => es_get_available_search_fields(),
                'attributes' => array(
                    'placeholder' => __( 'Select field', 'es' ),
                ),
                'after' => "</div>",
            ),

            'is_collapsed_filter_enabled' => array(
                'before' => '<hr/><h4>' . __( 'Collapsed filters', 'es' ) . '</h4>',
                'type' => 'switcher',
                'label' => __( 'Enable collapsed filters', 'es' ),
                'attributes' => array(
                    'data-toggle-container' => '#es-collapsed-filter-options--' . $this->id
                ),
            ),

            'collapsed_fields' => array(
                'before' => "<div id='es-collapsed-filter-options--{$this->id}'>",
                'type' => 'fields-list-selector',
                'options' => es_get_available_search_fields(),
                'attributes' => array(
                    'placeholder' => __( 'Select field', 'es' ),
                ),
                'after' => "</div></div>",
            ),

            'fields' => array(
                'label' => __( 'Select fields', 'es' ),
                'type' => 'fields-list-selector',
                'options' => es_get_available_search_fields(),
                'attributes' => array(
                    'placeholder' => __( 'Select field', 'es' ),
                ),
                'wrapper_class' => $instance['search_type'] != 'advanced' ? 'es-hidden' : '',
            ),
        ) );
	}
}

new Es_Search_Form_Widget();
