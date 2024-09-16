<?php

/**
 * Class Es_Widget.
 *
 * Base plugin class for estatik widgets.
 */
abstract class Es_Widget extends WP_Widget {

    const EXCLUDE_WP_PAGES = true;

	/**
	 * Es_Widget constructor.
	 *
	 * @param $id_base
	 * @param $name
	 * @param array $widget_options
	 * @param array $control_options
	 */
	public function __construct( $id_base, $name, array $widget_options = array(), array $control_options = array() ) {
		parent::__construct( $id_base, $name, $widget_options, $control_options );

		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}

	/**
	 * Register widget after widget init.
	 *
	 * @return void
	 */
	public function register_widget() {
		register_widget( get_called_class() );
	}

    /**
     * Return list of pages for page field.
     *
     * @param bool $exclude_wp_pages
     * @return mixed
     */
    public static function get_pages( $exclude_wp_pages = false ) {
        $system_pages = array();

        if ( ! $exclude_wp_pages ) {
            $system_pages = array(
                'archive_page'            => __( 'Archive page',  'es' ),
                'single_page'             => __( 'Single Page',   'es' ),
                'single_property_page'    => __( 'Single Property Page', 'es' ),
                'archive_property_page'   => __( 'Archive Property Page', 'es' ),
                'category_page'           => __( 'Category Page', 'es' ),
                'search_page'             => __( 'Search Page',   'es' ),
                'author_page'             => __( 'Author Page',   'es' ),
            );
        }

        $pages = get_pages();

        if ( ! empty( $pages ) ) {
            foreach ( $pages as $page ) {
                $system_pages[ $page->ID ] = $page->post_title;
            }
        }

        return apply_filters( 'es_get_widget_pages', $system_pages, $exclude_wp_pages );
    }

	/**
	 * Return default widget data.
	 *
	 * @return array
	 */
	public function get_default_data() {
		return array(
		    'page_access' => 'show',
        );
	}

    /**
     * Return widget fields array.
     *
     * @param $instance
     * @return array
     */
	public function get_widget_form( $instance ) {
	    return array();
	}

    /**
     * Return page access form fields config.
     *
     * @return array[]
     */
	public function get_page_access_fields() {
        $post_pages = get_pages();

        $pages = array();

        if ( $post_pages ) {
            foreach ( $post_pages as $page ) {
                $pages[ $page->ID ] = $page->post_title;
            }
        }

        return apply_filters( 'es_widget_get_page_access_fields', array(
            'page_access' => array(
                'label' => __( 'Show on pages', 'es' ),
                'type' => 'select',
                'options' => array(
                    'show' => __( 'Show on checked page', 'es' ),
                    'exclude' => __( 'Hide on checked page', 'es' ),
                ),
            ),
            'selected_pages' => array(
                'label' => __( 'Select pages', 'es' ),
                'type' => 'select',
                'attributes' => array(
                    'multiple' => 'multiple',
                    'class' => 'es-field__input js-es-tags',
                    'data-placeholder' => __( 'All pages', 'es' ),
                ),
                'options' => static::get_pages()
            ),
        ), $this );
    }

	/**
	 * Display widget form.
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$framework = es_framework_instance();
		$instance = ! empty( $instance ) ? $instance : $this->get_default_data();
		$form = apply_filters( 'es_widget_form', $this->get_widget_form( $instance ), $this );
		$form_builder = $framework->widget_fields_renderer( $form, $instance, $this );
		echo "<div class='es-widget__form " . $this->id_base . "'>";
		$form_builder->render();
		echo "</div>";
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
            <?php if ( ! empty( $instance['title'] ) ) :
				$title = apply_filters( 'es_widget_title', $instance['title'], $this, $args, $instance );
                echo $args['before_title'] . $title . $args['after_title'];
            endif; ?>
            <div class="es-widget-wrap">
                <?php $this->render( $instance ); ?>
            </div>
            <?php echo $args['after_widget'];
        }
	}

    /**
     * Is widget can render.
     *
     * @param array $instance
     *
     * @return bool
     */
	public function can_render( $instance = array() ) {
	    return apply_filters( 'es_widget_can_render', true, $instance, $this );
    }

    /**
     * Render widget on frontend.
     *
     * @param $instance
     *
     * @return void
     */
    public function render( $instance = array() ) {}
}
