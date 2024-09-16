<?php

use Elementor\Controls_Manager;

/**
 * Class Es_Elementor_Search_Form_Widget.
 */
class Elementor_Es_Properties_Slider_Widget extends Elementor_Es_Query_Widget {

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
		return 'es-slider-widget';
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
		return _x( 'Estatik Slider', 'widget name', 'es' );
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
        return 'es-icon es-icon_slider';
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
		$shortcode = es_get_shortcode_instance( 'es_properties_slider' );
		$attr = $shortcode->get_attributes();

		$this->start_controls_section(
			'section_content', array( 'label' => _x( 'Content', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'title', array(
			'label' => __( 'Title' ),
			'type' => Controls_Manager::TEXT,
			'default' => $attr['title'],
		) );

		$this->add_custom_control( 'layout', array(
			'label' => __( 'Layout', 'es' ),
			'type' => Controls_Manager::SELECT,
			'default' => $attr['layout'],
			'options' => array(
				'horizontal' => __( 'Horizontal', 'es' ),
				'vertical' => __( 'Vertical', 'es' ),
			),
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'es_slider_settings', array( 'label' => _x( 'Slider settings', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'slides_per_serving', array(
			'label' => __( 'Slides to show', 'es' ),
			'type' => Controls_Manager::NUMBER,
			'min' => 1,
			'default' => $attr['slides_per_serving'],
		) );

		$this->add_custom_control( 'autoplay', array(
			'label' => __( 'Autoplay', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attr['autoplay'] ? 'yes' : $attr['autoplay'],
		) );

		$this->add_custom_control( 'autoplay_timeout', array(
			'label' => __( 'Autoplay timeout', 'es' ),
			'type' => Controls_Manager::NUMBER,
			'default' => $attr['autoplay_timeout'],
		) );

		$this->add_custom_control( 'is_arrows_enabled', array(
			'label' => __( 'Enable arrows', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attr['is_arrows_enabled'] ? 'yes' : $attr['is_arrows_enabled'],
		) );

        $this->add_custom_control( 'infinite', array(
            'label' => __( 'Infinite', 'es' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => $attr['infinite'] ? 'yes' : $attr['infinite'],
        ) );

		$this->add_custom_control( 'space_between_slides', array(
			'label' => __( 'Space between slides (px)', 'es' ),
			'type' => Controls_Manager::NUMBER,
			'default' => $attr['space_between_slides'],
		) );

		do_action( 'es_el_properties_slider_end_controls', $this );

		$this->end_controls_section();

		$this->query_register_controls();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
//        $settings = wp_array_slice_assoc( $settings, $this->get_custom_controls_keys() );
		$shortcode = es_get_shortcode_instance( 'es_properties_slider', $settings );
		echo $shortcode->get_content();
	}
}