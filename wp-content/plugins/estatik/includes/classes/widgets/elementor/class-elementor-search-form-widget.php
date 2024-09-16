<?php

use Elementor\Controls_Manager;

/**
 * Class Es_Elementor_Search_Form_Widget.
 */
class Elementor_Es_Search_Form_Widget extends Elementor_Es_Base_Widget {

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
		return 'es-search-form-widget';
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
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return _x( 'Estatik Search', 'widget name', 'es' );
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
        /** @var Es_Search_Form_Shortcode $shortcode */
        $shortcode = es_get_shortcode_instance( 'es_search_form' );
        $attr = $shortcode->get_default_attributes();

        $this->start_controls_section('section_content', array( 'label' => _x( 'Content', 'Elementor widget section', 'es' ) ) );

        $this->add_control( 'title', array(
            'label' => __( 'Title' ),
            'type' => Controls_Manager::TEXT,
            'default' => $attr['title'],
        ) );

        $this->add_control( 'search_type', array(
            'label' => __( 'Search type', 'es' ),
            'type' => Controls_Manager::SELECT,
            'options' => array(
                'main' => _x( 'Main', 'search widget', 'es' ),
                'simple' => _x( 'Simple', 'search widget', 'es' ),
                'advanced' => _x( 'Advanced', 'search widget', 'es' ),
            ),
            'default' => $attr['search_type'],
        ) );

	    $this->add_control( 'background', array(
		    'label' => __( 'Background color', 'es' ),
		    'description' => __( 'Only for Main search layout', 'es' ),
		    'type' => Controls_Manager::COLOR,
		    'default' => $attr['background'],
	    ) );

		$this->add_control( 'enable_saved_search', array(
			'label' => __( 'Enable saved search', 'es' ),
			'description' => __( 'Only for Simple or Advanced search layout', 'es' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => $attr['enable_saved_search'] ? 'yes' : $attr['enable_saved_search'],
		) );

        $this->add_control( 'search_page_id', array(
            'label' => __( 'Search results page', 'es' ),
            'type' => Controls_Manager::SELECT,
            'options' => array( '' => __( 'WP default', 'es' ) ) + es_get_pages(),
            'default' => $attr['search_page_id'] ? $attr['search_page_id'] : '',
        ) );

        $this->add_control( 'is_address_search_enabled', array(
            'label' => __( 'Search by location', 'es' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => $attr['is_address_search_enabled'] ? 'yes' : $attr['is_address_search_enabled'],
        ) );

        $this->end_controls_section();

        $this->start_controls_section('simple_main_fields', array( 'label' => _x( 'Simple & main search fields', 'Elementor widget section', 'es' ) ) );

        $this->add_control( 'is_main_filter_enabled', array(
            'label' => __( 'Main filters', 'es' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => $attr['is_main_filter_enabled'] ? 'yes' : $attr['is_main_filter_enabled'],
        ) );

        $this->add_control( 'main_fields', array(
            'label' => __( 'Fields', 'es' ),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => es_get_available_search_fields(),
            'default' => $attr['main_fields'],
        ) );

        $this->add_control( 'is_collapsed_filter_enabled', array(
            'label' => __( 'Collapsed filters', 'es' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => $attr['is_collapsed_filter_enabled'] ? 'yes' : $attr['is_collapsed_filter_enabled'],
        ) );

        $this->add_control( 'collapsed_fields', array(
            'label' => __( 'Fields', 'es' ),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => es_get_available_search_fields(),
            'default' => $attr['collapsed_fields'],
        ) );

        $this->end_controls_section();

        $this->start_controls_section('advanced_fields', array( 'label' => _x( 'Advanced search fields', 'Elementor widget section', 'es' ) ) );

        $this->add_control( 'fields', array(
            'label' => __( 'Fields', 'es' ),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => es_get_available_search_fields(),
            'default' => $attr['fields'],
        ) );

        $this->end_controls_section();
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
        $settings = $this->get_settings_for_display();
        $shortcode = es_get_shortcode_instance( 'es_search_form', $settings );
        echo $shortcode->get_content();
    }
}