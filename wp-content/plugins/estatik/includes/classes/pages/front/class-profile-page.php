<?php

/**
 * Class Es_Profile_Page.
 */
class Es_Profile_Page {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_ajax_es_profile_save_info', array( 'Es_Profile_Page', 'ajax_save_profile_data' ) );
		add_action( 'wp_ajax_es_profile_save_pwd', array( 'Es_Profile_Page', 'ajax_save_profile_pwd' ) );
	}

	/**
	 * Save profile data via ajax.
	 */
	public static function ajax_save_profile_data() {
		$action = 'es_profile_save_info';

		$btn = '<a href="" class="es-btn es-btn--secondary js-es-close-popup">' . __( 'Got it', 'es' ) . '</a>';
		$error = __( 'Invalid security nonce. Please, reload the page and try again.', 'es' );
		$error_template = '<span class="es-icon es-icon_close"></span><h4>%s</h4><p>%s</p>';
		$error_title = _x( 'Error!', 'popup error title', 'es' );
		$message = sprintf( $error_template, $error_title,  $error );
		$response = es_error_ajax_response( $message . $btn );

		if ( check_ajax_referer( $action, 'es_profile_nonce' ) ) {
			if ( $user_id = get_current_user_id() ) {
				$user = es_get_user_entity( $user_id );
				$user_data = $user->get_wp_entity();
				$pwd = filter_input( INPUT_POST, 'es_confirm_password' );

				if ( wp_check_password( $pwd, $user_data->data->user_pass, $user_id ) || es_is_user_registered_via_social_network( $user_id ) ) {
					$name = es_clean( filter_input( INPUT_POST, 'es_user_name' ) );

					if ( empty( $_POST['avatar_id'] ) ) {
						$user->delete_field_value( 'avatar_id' );
					}

					$user_arr = array( 'ID' => $user_id );

					if ( ! empty( $name ) ) {
						$name = explode( ' ', $name );

						$user_arr['first_name'] = ! empty( $name[0] ) ? $name[0] : '';
						$user_arr['last_name'] = ! empty( $name[1] ) ? $name[1] : '';
					}

					$user_updated = wp_update_user( $user_arr );

					if ( is_wp_error( $user_updated ) ) {
						$message = sprintf( $error_template, $error_title, $user_updated->get_error_message() );
						$response = es_error_ajax_response( $message . $btn );
						es_set_wp_error_flash( 'profile', $user_updated );
					} else {
						do_action( 'es_after_save_profile', $user_updated );
						$success_template = '<span class="es-icon es-icon_check-mark"></span><h4>%s</h4>' . $btn;
						$response = es_success_ajax_response( sprintf( $success_template, __( 'Profile updated successfully.', 'es' ) ) );
					}

					if ( ! empty( $_FILES['avatar'] ) ) {
						if ( ! function_exists( 'wp_handle_upload' ) ) {
							require_once( ABSPATH . 'wp-admin/includes/image.php' );
							require_once( ABSPATH . 'wp-admin/includes/file.php' );
							require_once( ABSPATH . 'wp-admin/includes/media.php' );
						}

						if ( $attachment_id = media_handle_sideload( $_FILES['avatar'], -1 ) ) {
							if ( ! $attachment_id instanceof WP_Error ) {
								$user->save_field_value( 'avatar_id', $attachment_id );
							}
						}
					}
				} else {
					$message = sprintf( $error_template, $error_title, __( 'Password you entered doesn\'t match.', 'esa' ) );
					$response = es_error_ajax_response( $message . $btn );
				}
			} else {
				$message = sprintf( $error_template, $error_title, __( 'You are not authorize for access this page.', 'es' ) );
				$response = es_error_ajax_response( $message . $btn );
			}
		}

		$response['response_view'] = 'popup';
		$response['message'] = sprintf( "<div class='es-magnific-popup es-ajax-form-popup'>%s</div>", $response['message'] );

		wp_die( json_encode( apply_filters( 'es_save_profile_info_response', $response ) ) );
	}

	/**
	 * @return void
	 */
	public static function ajax_save_profile_pwd() {
		$btn = '<a href="" class="es-btn es-btn--secondary js-es-close-popup">' . __( 'Got it', 'es' ) . '</a>';
		$error = __( 'Invalid security nonce. Please, reload the page and try again.', 'es' );
		$error_template = '<span class="es-icon es-icon_close"></span><h4>%s</h4><p>%s</p>';
		$error_title = _x( 'Error!', 'popup error title', 'es' );
		$message = sprintf( $error_template, $error_title,  $error );
		$response = es_error_ajax_response( $message . $btn );

		if ( check_ajax_referer( 'es_profile_save_pwd', 'es_profile_pwd_nonce' ) ) {
			if ( $user_id = get_current_user_id() ) {
				$user_data = get_userdata( $user_id );
				$current_password = filter_input( INPUT_POST, 'es_current_password' );
				$new_password = filter_input( INPUT_POST, 'es_new_password' );
				$confirm_new_password = filter_input( INPUT_POST, 'es_confirm_password' );
				$has_error = false;

				if ( ! wp_check_password( $current_password, $user_data->data->user_pass ) ) {
					$message = sprintf( $error_template, $error_title, __( 'Current password didn\'t match.', 'es' ) );
					$response = es_error_ajax_response( $message . $btn );
					$has_error = true;
				}

				if ( $new_password !== $confirm_new_password ) {
					$message = sprintf( $error_template, $error_title, __( 'New password is not equal with confirm password field.', 'es' ) );
					$response = es_error_ajax_response( $message . $btn );
					$has_error = true;
				}

				if ( ! $has_error ) {
					$user_updated = wp_update_user( array(
						'user_pass' => $new_password,
						'ID' => $user_id
					) );

					if ( is_wp_error( $user_updated ) ) {
						$message = sprintf( $error_template, $error_title, $user_updated->get_error_message() );
						$response = es_error_ajax_response( $message . $btn );
					} else {
						$success_template = '<span class="es-icon es-icon_check-mark"></span><h4>%s</h4>' . $btn;
						$response = es_success_ajax_response( sprintf( $success_template, __( 'Password successfully changed.', 'es' ) ) );
					}
				}
			}
		}

		$response['response_view'] = 'popup';
		$response['message'] = sprintf( "<div class='es-magnific-popup es-ajax-form-popup'>%s</div>", $response['message'] );

		wp_die( json_encode( apply_filters( 'es_save_profile_pwd_response', $response ) ) );
	}
}

Es_Profile_Page::init();
