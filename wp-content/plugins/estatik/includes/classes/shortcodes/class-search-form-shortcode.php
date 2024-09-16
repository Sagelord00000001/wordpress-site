<?php

/**
 * Class Es_Search_Form_Shortcode.
 */
class Es_Search_Form_Shortcode extends Es_Shortcode {

    /**
     * Return search shortcode DOM.
     *
     * @return string|void
     */
    public function get_content() {
        $template = sprintf( 'front/shortcodes/search/%s.php', $this->_attributes['search_type'] );

        foreach ( array( 'fields', 'collapsed_fields', 'main_fields' ) as $fields ) {
            if ( ! empty( $this->_attributes[ $fields ] ) && is_string( $this->_attributes[ $fields ] ) ) {
                $this->_attributes[ $fields ] = explode( ',', $this->_attributes[ $fields ] );
	            $this->_attributes[ $fields ] = array_map( 'trim', $this->_attributes[ $fields ] );
            }
        }

		$search_page_id = $this->_attributes['search_page_id'];

        ob_start();
        es_load_template( $template, array(
            'shortcode_instance' => $this,
            'attributes' => $this->_attributes,
            'search_page_id' => $this->_attributes['search_page_id'],
            'container_classes' => $this->get_container_classes(),
            'search_page_uri' => get_permalink( $this->_attributes['search_page_id'] ),
	        'search_page_exists' => ! empty( $search_page_id ) && get_post_status( $search_page_id ) == 'publish'
        ) );
        return ob_get_clean();
    }

    /**
     * Get widget icon.
     *
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'es-icon es-icon_search-form';
    }

    /**
     * Return search form container css classes.
     *
     * @return mixed|void
     */
    public function get_container_classes() {
        $atts = $this->_attributes;
        $classes[] = 'es-search';
        $classes[] = 'js-es-search';
        $classes[] = 'et_smooth_scroll_disabled';

        if ( empty( $atts['enable_ajax'] ) ) {
	        $classes[] = 'es-search--ignore-ajax';
        }

        $classes[] = sprintf( 'es-search--%s', $atts['search_type'] );

        if ( ! empty( $atts['container_classes'] ) ) {
            $container_classes = explode( ' ', $atts['container_classes'] );

            if ( $container_classes ) {
                $classes = array_merge( $classes, $container_classes );
            }
        }

        if ( empty( $atts['is_address_search_enabled'] ) ) {
            $classes[] = "es-search--address-disabled";
        }

        if ( $atts['search_type'] === 'advanced' ) {
            $classes[] = sprintf( 'es-search--%s', $atts['layout'] );
        }

        $classes[] = sprintf( 'js-es-search--%s', $atts['search_type'] );

        return apply_filters( 'es_search_form_get_container_classes', implode( ' ', $classes ), $classes, $atts, $this );
    }

    /**
     * Return shortcode default attributes.
     *
     * @return array|void
     */
    public function get_default_attributes() {
        return apply_filters( sprintf( '%s_default_attributes', static::get_shortcode_name() ), array(
            'layout' => 'vertical',
            'address_placeholder' => ests( 'address_search_placeholder' ),
            'search_type' => 'advanced',
            'title' => __( 'Find Your Perfect Home', 'es' ),
            'padding' => '30px 10%',
            'search_page_id' => ests( 'search_results_page_id' ),
            'is_address_search_enabled' => 1,
            'enable_saved_search' => ests( 'is_saved_search_enabled' ),
            'background' => '',
            'enable_ajax' => false,
            'is_main_filter_enabled' => true,
            'is_collapsed_filter_enabled' => true,
            'main_fields' => array( 'price', 'es_type', 'bedrooms', 'bathrooms' ),
            'collapsed_fields' => array( 'half_baths', 'es_amenity', 'es_feature', 'area', 'lot_size', 'floors' ),

            // Fields list for advances search type.
            'fields' => array( 'price', 'es_category', 'es_type', 'es_rent_period',
                'bedrooms', 'bathrooms', 'half_baths', 'es_amenity', 'es_feature',
                'area', 'lot_size', 'floors' ),
        ) );
    }

    /**
     * @return Exception|string
     */
    public static function get_shortcode_name() {
        return 'es_search_form';
    }
}
