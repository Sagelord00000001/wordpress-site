<?php

use Elementor\Controls_Manager;

/**
 * Class Es_Elementor_Search_Form_Widget.
 */
class Elementor_Es_Authentication_Widget extends Elementor_Es_Base_Widget {

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
        return 'es-auth-widget';
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
        return _x( 'Estatik Authentication', 'widget name', 'es' );
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
        return 'es-icon es-icon_authentication';
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
        $shortcode = es_get_shortcode_instance( 'es_authentication' );
        $attr = $shortcode->get_default_attributes();

        $this->start_controls_section(
            'section_content', array( 'label' => _x( 'Content', 'Elementor widget section', 'es' ), )
        );

        $this->add_control( 'auth_item', array(
            'label' => __( 'Form', 'es' ),
            'type' => Controls_Manager::SELECT,
            'default' => $attr['auth_item'],
            'options' => array(
                'login-form' => __( 'Login form', 'es' ),
                'login-buttons' => __( 'Login buttons', 'es' ),
                'buyer-register-form' => __( 'Buyer register form', 'es' ),
                'buyer-register-buttons' => __( 'Buyer register buttons', 'es' ),
                'reset-form' => __( 'Reset password', 'es' ),
            ),
        ) );

        $this->add_control( 'enable_facebook', array(
            'label' => __( 'Enable Facebook auth', 'es' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => $attr['enable_facebook'] ? 'yes' : $attr['enable_facebook'],
        ) );

        $this->add_control( 'enable_google', array(
            'label' => __( 'Enable Google auth', 'es' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => $attr['enable_google'] ? 'yes' : $attr['enable_google'],
        ) );

        $this->add_control( 'enable_login_form', array(
            'label' => __( 'Enable Login form', 'es' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => $attr['enable_login_form'] ? 'yes' : $attr['enable_login_form'],
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
        $shortcode = es_get_shortcode_instance( 'es_authentication', $settings );
        echo $shortcode->get_content();
    }
}
