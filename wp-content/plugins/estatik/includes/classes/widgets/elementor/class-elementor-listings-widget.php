<?php

use Elementor\Controls_Manager;

/**
 * Class Es_Elementor_Search_Form_Widget.
 */
class Elementor_Es_Listings_Widget extends Elementor_Es_Query_Widget {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'es-listings-widget';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return _x( 'Estatik Listings', 'widget name', 'es' );
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
        return 'es-icon es-icon_listings';
    }

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		/** @var Es_Request_Form_Shortcode $shortcode */
		$shortcode = es_get_shortcode_instance( 'es_my_listing' );
		$attributes = $shortcode->get_attributes();

		$this->start_controls_section(
			'es_section_content', array( 'label' => _x( 'Content', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'page_title', array(
			'label' => __( 'Title', 'es' ),
			'type' => Controls_Manager::TEXT,
			'default' => $attributes['page_title'],
		) );

		$this->add_custom_control( 'layout', array(
			'label' => __( 'Layout', 'es' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'list' => _x( 'List', 'listings layout name', 'es' ),
				'1_col' => _x( '1 column', 'listings layout name', 'es' ),
				'2_col' => _x( '2 columns', 'listings layout name', 'es' ),
				'3_col' => _x( '3 columns', 'listings layout name', 'es' ),
				'4_col' => _x( '4 columns', 'listings layout name', 'es' ),
				'5_col' => _x( '5 columns', 'listings layout name', 'es' ),
				'6_col' => _x( '6 columns', 'listings layout name', 'es' ),
			),
			'default' => $attributes['layout']
		) );

        $this->add_custom_control( 'disable_pagination', array(
            'label' => __( 'Disable pagination', 'es' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => $attributes['disable_pagination'] ? 'yes' : $attributes['disable_pagination'],
        ) );

        $this->add_custom_control( 'view_all_link_name', array(
            'label' => __( 'View all link name', 'es' ),
            'type' => Controls_Manager::TEXT,
            'default' => $attributes['view_all_link_name'],
        ) );

        $this->add_custom_control( 'view_all_page_id', array(
            'label' => __( 'View all page', 'es' ),
            'type' => Controls_Manager::SELECT2,
            'options' => es_get_pages(),
        ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'es_listings_navbar', array( 'label' => _x( 'Navbar', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'disable_navbar', array(
			'label' => __( 'Disable navbar', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['disable_navbar'] ? 'yes' : $attributes['disable_navbar']
		) );

		$this->add_custom_control( 'show_sort', array(
			'label' => __( 'Show sorting', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['show_sort'] ? 'yes' : $attributes['show_sort'],
		) );

		$this->add_custom_control( 'show_page_title', array(
			'label' => __( 'Show page title', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['show_page_title'] ? 'yes' : $attributes['show_page_title'],
		) );

		$this->add_custom_control( 'show_total', array(
			'label' => __( 'Show total', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['show_total'] ? 'yes' : $attributes['show_total'],
		) );

		$this->add_custom_control( 'show_layouts', array(
			'label' => __( 'Show layouts', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['show_layouts'] ? 'yes' : $attributes['show_layouts'],
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'es_search_navbar', array( 'label' => _x( 'Search navbar', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'enable_search', array(
			'label' => __( 'Enable search bar', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attributes['enable_search'] ? 'yes' : $attributes['enable_search'],
		) );

		$this->add_custom_control( 'main_fields', array(
			'label' => __( 'Visible fields', 'es' ),
			'type' => Controls_Manager::SELECT2,
			'options' => es_get_available_search_fields(),
			'multiple' => true,
			'default' => array( 'price', 'es_type', 'bedrooms', 'bathrooms' ),
		) );

		$this->add_custom_control( 'collapsed_fields', array(
			'label' => __( 'Collapsed fields', 'es' ),
			'type' => Controls_Manager::SELECT2,
			'options' => es_get_available_search_fields(),
			'multiple' => true,
			'default' => array( 'half_baths', 'es_amenity', 'es_feature', 'area', 'lot_size', 'floors' ),
		) );

		$this->end_controls_section();

		$this->query_register_controls();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param array $instance
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();
		$settings = wp_array_slice_assoc( $settings, $this->get_custom_controls_keys() );
		$settings = static::prepare_values( $settings );
		$shortcode = es_get_shortcode_instance( 'es_my_listing', $settings );
		unset( $this->_custom_controls_keys );
		echo $shortcode->get_content();
	}
}