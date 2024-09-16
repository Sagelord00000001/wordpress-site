<?php

/**
 * Class Es_Properties_Slider_Shortcode.
 */
class Es_Properties_Slider_Shortcode extends Es_My_Listing_Shortcode {

    /**
     * Return search shortcode DOM.
     *
     * @return string|void
     */
    public function get_content() {
        $query = new WP_Query( $this->get_query_args() );
        ob_start();

        $template = apply_filters( 'es_properties_slider_shortcode_item_template', 'front/property/content-archive.php',
            $this->_attributes, $this );

        es_load_template( 'front/shortcodes/properties-slider.php', array(
            'query' => $query,
            'attributes' => $this->_attributes,
            'item_template' => $template,
            'slider_config' => static::get_slider_config( $this->_attributes ),
            'container_classes' => static::get_container_classes( $this->_attributes ),
        ) );

        return ob_get_clean();
    }

    /**
     * @param $attributes array
     *
     * @return string
     */
    public static function get_container_classes( $attributes ) {
        $classes = array(
            'es-listings',
            'es-listings--grid',
            'es-listings--ignore-responsive',
            'es-properties-slider',
			'slick-hidden',
            'js-es-slick' );

        return apply_filters( 'es_proeprties_slider_shortcode_container_classes', implode( ' ', $classes ), $classes, $attributes );
    }

    /**
     * Return shortcode name.
     *
     * @return Exception|string
     */
    public static function get_shortcode_name() {
        return 'es_properties_slider';
    }

    /**
     * Return shortcode default args.
     *
     * @return array|void
     */
    public function get_default_attributes() {
        $args = array(
            'id' => 'es-properties-slider-' . uniqid(),
            'layout' => 'horizontal',
            'slides_per_serving' => 3,
            'is_arrows_enabled' => 1,
            'space_between_slides' => 24,
            'autoplay' => true,
            'autoplay_timeout' => 5000,
            'limit' => 20,
            'infinite' => true,
	        'title' => __( 'Recommended Listings', 'es' ),
            'ignore_search' => 1,
        );

        return es_parse_args( $args, parent::get_default_attributes() );
    }

    /**
     * Generate and return array config for js carousel.
     *
     * @retunr void
     *
     * @param $attributes array
     *
     * @return array
     */
    public static function get_slider_config( $attributes ) {
        $config = array(
            'slidesToShow' => $attributes['slides_per_serving'],
            'arrows' => (bool) $attributes['is_arrows_enabled'],
            'autoplay' => (bool) $attributes['autoplay'],
            'autoplaySpeed' => $attributes['autoplay_timeout'],
            'infinite' => boolval( $attributes['infinite'] ),
            'pauseOnHover' => true,
            'slide' => 'div',
            'rows' => 0,
            'adaptiveHeight' => true,
            'prevArrow' => "<span class='slick-prev'><span class='es-icon es-icon_chevron-left slick-prev'></span></span>",
            'nextArrow' => "<span class='slick-next'><span class='es-icon es-icon_chevron-right slick-next'></span></span>",
        );

        if ( in_array( $attributes['layout'], array( 'v', 've', 'vert', 'vertical' ) ) ) {
            $config['vertical'] = true;
            $config['verticalSwiping'] = true;
            $config['prevArrow'] = "<span class='slick-prev'><span class='es-icon es-icon_chevron-top slick-prev'></span></span>";
            $config['nextArrow'] = "<span class='slick-next'><span class='es-icon es-icon_chevron-bottom slick-prev'></span></span>";
        }

	    if ( ! empty( $attributes['prevArrow'] ) ) {
		    $config['prevArrow'] = $attributes['prevArrow'];
	    }

	    if ( ! empty( $attributes['nextArrow'] ) ) {
		    $config['nextArrow'] = $attributes['nextArrow'];
	    }

        return apply_filters( sprintf( '%s_slider_config', static::get_shortcode_name() ), $config );
    }
}
