<?php

/**
 * Class Es_Admin.
 */
class Es_Admin {

	/**
	 * @return void
	 */
	public static function init() {
        add_action( 'wp', array( 'Es_Admin', 'notices_add_schedules' ) );
		add_action( 'es_admin_page_bar', array( 'Es_Admin', 'page_bar' ) );
		add_filter( 'cron_schedules', array( 'Es_Admin', 'add_cron_intervals' ) );
        add_action( 'es_remote_admin_notices', array( 'Es_Admin', 'check_for_notices' ) );
		add_action( 'admin_notices', array( 'Es_Admin', 'render_notices' ) );
		add_action( 'wp_ajax_es_dismiss_notices', array( 'Es_Admin', 'dismiss_notices' ) );
	}

    public static function dismiss_notices() {
	    if ( ! empty( $_POST['notice'] ) && current_user_can( 'manage_options' ) && check_ajax_referer( 'es_dismiss_notices' ) ) {
		    update_option( sanitize_text_field( $_POST['notice'] ), 1 );
	    }
    }

	/**
	 * Cron handler for search remote estatik admin notices.
     *
     * @return void
	 */
    public static function check_for_notices() {
        $url = add_query_arg( array(
            'version' => Estatik::get_plugin_type(),
            'type' => Estatik::get_plugin_type(),
            'product' => 'estatik-plugin',
        ), 'https://estatik.net/request-banner.php' );
	    $notices = wp_remote_get( $url );

        if ( ! $notices instanceof WP_Error ) {
            $response = wp_remote_retrieve_body( $notices );
            $response = json_decode( $response );

            if ( $response && ! empty( $response->banners ) ) {
                update_option( 'estatik-banners', $response );
            } else {
	            update_option( 'estatik-banners', null );
            }
        }
    }

	/**
	 * Render remote admin notices.
     *
     * @return void
	 */
    public static function render_notices() {
        $banners = get_option( 'estatik-banners' );
        if ( $banners && ! empty( $banners->banners ) ) {
            $banners = (array) $banners->banners;
            $curtime = time();

            foreach ( $banners as $key => $b ) {
                $dissmissied = get_option( sanitize_text_field( $key ) );
	            $b = (array) $b;
	            if ( ! $dissmissied && ! empty( $b['from'] ) && $b['from'] <= $curtime && ! empty( $b['to'] ) && $b['to'] >= $curtime ) {
                    echo $b['styles'] . $b['content'];
	            }
            }
        }
    }

	/**
	 * @param $intervals
	 *
	 * @return mixed
	 */
    public static function add_cron_intervals( $intervals ) {
        if ( empty( $intervals['5min'] ) ) {
	        $intervals['5min'] = array(
		        'interval' => 300,
                'display' => __( 'Each 5 min', 'es' ),
            );
        }

        return $intervals;
    }

	/**
	 * Add wp schedules for manage expired subscriptions
	 *
	 * return @void
	 */
	public static function notices_add_schedules() {
		if ( ! wp_next_scheduled( 'es_remote_admin_notices' ) ) {
			wp_schedule_event( time(), '5min', 'es_remote_admin_notices' );
		}
	}

	/**
	 * @return void
	 */
	public static function page_bar() {
		$items = apply_filters( 'es_admin_page_bar_items', array(
			'es_fields_builder' => __( 'Fields builder', 'es' ),
			'es_data_manager' => __( 'Data manager', 'es' )
		) );

		$page = filter_input( INPUT_GET, 'page' ); ?>

		<div class="es-page-bar">
			<ul>
				<?php foreach ( $items as $page_id => $label ) : ?>
					<li class="<?php echo $page == $page_id ? 'active' : ''; ?>"><a href="<?php echo admin_url( "admin.php?page={$page_id}" ); ?>"><?php echo $label; ?></a></li>
				<?php endforeach; ?>
			</ul>

			<?php if ( $page == 'es_fields_builder' ) : ?>
				<button class="es-btn es-btn--secondary js-es-fields-builder-add-field" data-section-machine-name="basic-facts">
					<span class="es-icon es-icon_plus"></span>
					<?php _e( 'Add field', 'es' ); ?>
				</button>
			<?php endif; ?>
		</div>
		<?php
	}
}

Es_Admin::init();
