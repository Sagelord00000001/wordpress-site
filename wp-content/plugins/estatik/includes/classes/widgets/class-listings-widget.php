<?php

/**
 * Class Es_Listings_Widget.
 */
class Es_Listings_Widget extends Es_Properties_Filter_Widget {

    /**
     * Es_Widget_Example constructor.
     */
    public function __construct() {
        parent::__construct( 'es-listings', _x( 'Estatik Listings', 'widget name', 'es' ) );
    }

    /**
     * Render widget content.
     *
     * @param $instance array
     *
     * @return void
     */
    public function render( $instance = array() ) {
        $instance['disable_navbar'] = true;
        $instance['disable_pagination'] = true;
        /** @var Es_My_Listing_Shortcode $shortcode */
        $shortcode = es_get_shortcode_instance( 'es_my_listing', $instance );
        echo $shortcode->get_content();
    }

    /**
     * Return default widget data.
     *
     * @return array
     */
    public function get_default_data() {
        $shortcode = es_get_shortcode_instance( 'es_my_listing' );
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
        );

        $config = array_merge( $config, $this->get_page_access_fields(), array(
            'layout' => array(
                'label' => __( 'Layout', 'es' ),
                'type' => 'select',
                'options' => array(
                    'list' => _x( 'List', 'listings layout name', 'es' ),
                    '1_col' => _x( '1 column', 'listings layout name', 'es' ),
                    '2_col' => _x( '2 columns', 'listings layout name', 'es' ),
                    '3_col' => _x( '3 columns', 'listings layout name', 'es' ),
                    '4_col' => _x( '4 columns', 'listings layout name', 'es' ),
                    '5_col' => _x( '5 columns', 'listings layout name', 'es' ),
                    '6_col' => _x( '6 columns', 'listings layout name', 'es' ),
                ),
            ),
        ) );

        $parent_form = parent::get_widget_form( $instance );

        unset( $parent_form['show_properties_by'] );

        $parent_form['view_all_link_name'] = array(
            'type' => 'text',
            'label' => __( 'Link button name', 'es' ),
        );

        $pages = static::get_pages( static::EXCLUDE_WP_PAGES );

        $parent_form['view_all_page_id'] = array(
            'type' => 'select',
            'label' => __( 'Link button page', 'es' ),
            'options' => $pages,
        );

        return array_merge( $config, $parent_form );
    }
}

new Es_Listings_Widget();
