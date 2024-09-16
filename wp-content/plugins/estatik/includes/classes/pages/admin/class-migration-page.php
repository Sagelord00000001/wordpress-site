<?php

/**
 * Class Es_Migration_Page.
 */
class Es_Migration_Page {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_ajax_es_migration', array( get_called_class(), 'migration_handler' ) );
	}

	/**
	 * @return void
	 */
	public static function migration_handler() {
		if ( check_ajax_referer( 'es_migration', 'es_migration' ) ) {
			include ES_PLUGIN_CLASSES . '/class-plugin-migration.php';

			// Disable time limit for long-term operations.
			set_time_limit( 0 );

			if ( ! empty( $_POST ) ) {
				unset( $_POST['messages'] );
				$response = $_POST;
			}

			// Set ajax action for response.
			$response['action'] = 'es_migration';
			$response['es_migration'] = es_post( 'es_migration' );

			if ( ! taxonomy_exists( 'es_labels' ) ) {
				register_taxonomy( 'es_labels', 'properties' );
			}

			if ( empty( es_post( 'progress' ) ) ) {
				$response['messages']['info'][] = __( 'Start migration.', 'es' );
				$response['progress'] = 1;
			} else {
				$break = false;
				global $wpdb;

				if ( empty( $response['pages_migrated'] ) ) {
					Es_Plugin_Migration::migrate_pages();
					$response['pages_migrated'] = 1;
					$response['messages']['success'][] = __( 'Pages migrated.', 'es' );
					$response['progress'] += 8;
					$break = true;
				}

				if ( empty( $response['settings_migrated'] ) ) {
					Es_Plugin_Migration::migrate_settings();
					$response['settings_migrated'] = 1;
					$response['messages']['success'][] = __( 'Plugin settings migrated.', 'es' );
					$response['progress'] += 9;
					$break = true;
				}

				if ( empty( $response['fb_migrated'] ) && ! $break ) {
					Es_Plugin_Migration::migrate_fb();
					$response['fb_migrated'] = 1;
					$response['messages']['success'][] = __( 'Fields Builder migrated.', 'es' );
					$response['progress'] += 10;
					$break = true;
				}

				if ( empty( $response['buyers_migrated'] ) && ! $break ) {
					$user_ids = $wpdb->get_col( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='wp_capabilities' AND meta_value LIKE '%es_buyer%'" );
					if ( $user_ids ) {
						foreach ( $user_ids as $user_id ) {
							Es_Plugin_Migration::migrate_buyer( $user_id );
						}
					}
					$response['buyers_migrated'] = 1;
					$response['messages']['success'][] = __( 'Buyers migrated.', 'es' );
					$response['progress'] += 10;
					$break = true;
				}

//				if ( empty( $response['mls_migrated'] ) && ! $break ) {
//					Es_Plugin_Migration::migrate_mls();
//					$response['mls_migrated'] = 1;
//					$response['messages']['success'][] = __( 'MLS Settings migrated.', 'es' );
//					$response['progress'] += 10;
//					$break = true;
//				}

				if ( empty( $response['labels_migrated'] ) && ! $break ) {
					Es_Plugin_Migration::migrate_labels();
					$response['labels_migrated'] = 1;
					$response['messages']['success'][] = __( 'Listings labels migrated.', 'es' );
					$response['progress'] += 10;
					$break = true;
				}

				if ( empty( $response['listings_migrated'] ) && ! $break ) {
					if ( empty( $response['listings_migration_started'] ) ) {
						$response['post_ids'] = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type='properties'" );
						$total = ! empty( $response['post_ids'] ) ? count( $response['post_ids'] ) : 0;

						if ( ! $total ) {
							$response['listings_migrated'] = true;
							$break = false;
						} else {
							/* translators: %1$s: listing (s), %2$s: total. */
							$response['messages']['info'][] = sprintf( __( 'Start migration %1$s %2$s.', 'es' ), $total, _n(  'listing', 'listings', $total ) );
							$response['listings_migration_started'] = 1;
							$break = true;
						}
					} else {
						if ( ! empty( $response['post_ids'] ) ) {
							$progress_diff = 100 - $response['progress'];
							$response['post_ids'] = explode( ',', $response['post_ids'] );
							$percentage = round( $progress_diff / count( $response['post_ids'] ), 2 );
							foreach ( $response['post_ids'] as $key => $post_id ) {
								Es_Plugin_Migration::migrate_listing( $post_id );
								/* translators: %s: listing id. */
								$response['messages']['success'][] = sprintf( __( 'Listing #%s successfully migrated.', 'es' ), $post_id );
								$response['progress'] += $percentage;
								$response['progress'] = round( $response['progress'], 2 );
								unset( $response['post_ids'][ $key ] );
								break;
							}
							if ( ! empty( $response['post_ids'] ) ) {
								$response['post_ids'] = implode( ',', $response['post_ids'] );
							}
							$break = true;
						} else {
							$response['listings_migrated'] = 1;
							$response['messages']['success'][] = __( 'Listings successfully migrated.', 'es' );
						}
					}
				}

				if ( ! $break ) {
					$response['messages']['success'][] = __( 'Migration successfully finished.', 'es' );
					$response['progress'] = 100;
					$response['done'] = true;
					$response['redirect_url'] = admin_url( 'edit.php?post_type=properties' );
					es_set_migration_as_executed();
				}
			}

			$response = apply_filters( 'es_migration_response', $response );

			wp_die( json_encode( $response ) );
		}
	}

	/**
	 * @return void
	 */
	public static function render() {
		$f = es_framework_instance();
		$f->load_assets();
		wp_enqueue_style( 'es-migration', ES_PLUGIN_URL . '/admin/css/migration.min.css', array( 'es-admin', 'estatik-progress' ), Estatik::get_version() );
		wp_enqueue_script( 'es-migration', ES_PLUGIN_URL . '/admin/js/migration.min.js', array( 'jquery', 'estatik-progress' ), Estatik::get_version() );
		wp_localize_script( 'es-migration', 'Estatik_Migration', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'listingsLink' => admin_url( 'edit.php?post_type=properties' ),
			'tr' => array(
				'internal_error' => __( 'Internal server error', 'es' ),
			)
		) );
		es_load_template( 'admin/migration/index.php' );
	}
}

Es_Migration_Page::init();
