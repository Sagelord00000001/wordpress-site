<?php

use Elementor\Controls_Manager;

/**
 * Class Es_Elementor_Search_Form_Widget.
 */
class Elementor_Es_Request_Form_Widget extends Elementor_Es_Base_Widget {

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
		return 'es-request-form-widget';
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
		return _x( 'Estatik Request Form', 'widget name', 'es' );
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
        return 'es-icon es-icon_request-form';
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
		$shortcode = es_get_shortcode_instance( 'es_request_form' );
		$attr = $shortcode->get_default_attributes();

		$this->start_controls_section(
			'section_content', array( 'label' => _x( 'Content', 'Elementor widget section', 'es' ), )
		);

		$this->add_control( 'title', array(
			'label' => __( 'Title' ),
            'type' => Controls_Manager::TEXT,
			'default' => $attr['title'],
		) );

		$this->add_control( 'message', array(
			'label' => __( 'Message' ),
            'type' => Controls_Manager::TEXTAREA,
			'default' => $attr['message'],
		) );

		$this->add_control( 'disable_name', array(
			'label' => __( 'Disable name', 'es' ),
            'type' => Controls_Manager::SWITCHER,
			'default' => $attr['disable_name'] ? 'yes' : $attr['disable_name'],
		) );

		$this->add_control( 'disable_tel', array(
			'label' => __( 'Disable phone', 'es' ),
            'type' => Controls_Manager::SWITCHER,
			'default' => $attr['disable_tel'] ? 'yes' : $attr['disable_tel'],
		) );

		$this->add_control( 'disable_email', array(
			'label' => __( 'Disable e-mail', 'es' ),
            'type' => Controls_Manager::SWITCHER,
			'default' => $attr['disable_email'] ? 'yes' : $attr['disable_email'],
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'es_styles', array( 'label' => _x( 'Style', 'Elementor widget section', 'es' ), )
		);

		$this->add_control( 'background', array(
			'label' => __( 'Background color', 'es' ),
			'type' => Controls_Manager::COLOR,
			'default' => $attr['background'],
		) );

		$this->add_control( 'color', array(
			'label' => __( 'Text color', 'es' ),
			'type' => Controls_Manager::COLOR,
			'default' => $attr['color'],
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_email', array( 'label' => _x( 'Email', 'Elementor widget section', 'es' ), )
		);

		$this->add_control( 'subject', array(
			'label' => _x( 'Subject', 'email subject', 'es' ),
			'type' => Controls_Manager::TEXT,
			'default' => $attr['subject'],
		) );

		$this->add_control( 'recipient_type', array(
			'label' => __( 'Send Message To', 'es' ),
			'type' => Controls_Manager::SELECT,
			'options' => $shortcode::get_send_to_list(),
			'default' => $attr['recipient_type'],
		) );

		$this->add_control( 'custom_email', array(
			'label' => __( 'Custom emails', 'es' ),
			'type' => Controls_Manager::TEXT,
			'default' => $attr['custom_email'],
		) );

		$this->end_controls_section();
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
		$shortcode = es_get_shortcode_instance( 'es_request_form', $settings );
		echo $shortcode->get_content();
	}
}
