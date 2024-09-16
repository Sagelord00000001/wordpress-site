<?php

/**
 * Class Es_Auth.
 */
class Es_Auth {

	/**
	 * @var int|null|WP_Error
	 */
	public static $new_user_id;

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( 'Es_Auth', 'register' ) );
		add_action( 'init', array( 'Es_Auth', 'login' ) );
		add_action( 'init', array( 'Es_Auth', 'retrieve_password' ) );
		add_action( 'init', array( 'Es_Auth', 'reset_password' ) );

		if ( ! empty( $_GET['auth_network'] ) ) {
			add_action( 'init', array( 'Es_Auth', 'social_login' ) );
		}

		add_action( 'es_register_new_buyer', array( 'Es_Auth', 'register_buyer' ) );
	}

	/**
	 * @param $data
	 */
	public static function register_buyer( $user_data ) {
		static::$new_user_id = wp_insert_user( $user_data );
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function wrap_email( $data ) {
		ob_start();
		$data['message'] = wpautop( $data['message'] );
		es_load_template( 'common/emails/template.php', array( 'content' => $data['message'] ) );
		$data['message'] = ob_get_clean();

		if ( empty( $data['headers'] ) && is_string( $data['headers'] ) ) {
			$data['headers'] = array();
		}
		$data['headers']['content-type'] = 'text/html';

		return $data;
	}

	/**
	 * @param $wp_new_user_notification_email array
	 * @param $user WP_User
	 *
	 * @return array|void
	 */
	public static function user_notification_email( $wp_new_user_notification_email, $user ) {
		$key = get_password_reset_key( $user );
		if ( is_wp_error( $key ) ) {
			return;
		}

		$user_login = $user->user_login;

		switch_to_locale( get_user_locale( $user ) );

		$query_args = array(
			'auth_item' => 'reset-form',
			'key' => $key,
			'login' => rawurlencode( $user_login ),
		);

		$login_page_id = ests( 'login_page_id' );

		if ( $login_page_id && get_post_status( $login_page_id ) == 'publish' ) {
			$link = add_query_arg( $query_args, get_permalink( $login_page_id ) );
		} else {
			$link = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );
		}

		/* translators: %s: User login. */
		$message  = sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
		/* translators: %s: reset pwd url. */
		$message .= sprintf( __( 'To set your password, visit the following <a href="%s">link</a>.', 'es' ), $link ) . "\r\n\r\n";

		$wp_new_user_notification_email['message'] = $message;

		return $wp_new_user_notification_email;
	}

	/**
	 * Register new user using register form.
	 *
	 * @return void
	 */
	public static function register() {
		$uniqid = es_post( 'uniqid' );
		if ( wp_verify_nonce( es_get_nonce( 'es_register_nonce_' . $uniqid ), 'es_register' ) ) {
			$is_popup = es_post( 'is_popup' );
			$type = es_post( 'es_type' );

			if ( es_verify_recaptcha() ) {
				$email = es_post( 'es_user_email', 'es_clean' );
				$password = filter_input( INPUT_POST, 'es_user_password' );

				$redirect_url = filter_input( INPUT_POST, 'redirect_url' );

				if ( $user_id = email_exists( $email ) ) {
					$auth_list = es_get_auth_networks_list();
					$social_network_exists = false;

					$user = wp_signon( array(
						'user_login' => es_clean( filter_input( INPUT_POST, 'es_user_login' ) ),
						'user_password' => filter_input( INPUT_POST, 'es_user_password' ),
						'remember' => true,
					), is_ssl() );

					if ( $user instanceof WP_User ) {
						wp_safe_redirect( $redirect_url ? add_query_arg( 'redirect_action', 'sign-up', $redirect_url ) : es_get_success_auth_redirect_url() );
						die;
					} else {
						foreach ( $auth_list as $auth_item ) {
							if ( get_user_meta( $user_id, 'auth_' . $auth_item, true ) ) {
								$social_network_exists = true;
								/* translators: %s: social network name. */
								es_set_flash( 'authenticate', sprintf( __( 'You already created an account using %s. Please log in instead.', 'es' ), ucfirst( $auth_item ) ), 'error' );
								break;
							}
						}

						if ( ! $social_network_exists ) {
							es_set_flash( 'authenticate', __( 'The email address you use has already been registered. Please log in.', 'es' ), 'error' );
						}

						wp_safe_redirect( es_get_auth_page_uri( 'buyer-register-form', $_SERVER['HTTP_REFERER'], $is_popup ) );
						die;
					}
				} else {
					do_action( "es_register_new_{$type}", array(
						'user_login' => $email,
						'user_pass'  => $password,
						'user_password'  => $password,
						'user_email' => $email,
					) );

					$user_id = static::$new_user_id;

					if ( empty( $user_id ) ) {
						es_set_flash( 'authenticate', __( 'User is not created. Incorrent user type.', 'es' ), 'error' );
					} else if ( is_wp_error( $user_id ) ) {
						es_set_wp_error_flash( 'authenticate', $user_id );

						wp_safe_redirect( es_get_auth_page_uri( "{$type}-register-form", $_SERVER['HTTP_REFERER'], $is_popup ) );
						die;
					} else {
						add_filter( 'wp_new_user_notification_email_admin', array( 'Es_Auth', 'wrap_email' ) );
						add_filter( 'wp_new_user_notification_email', array( 'Es_Auth', 'user_notification_email' ), 10, 2 );
						add_filter( 'wp_new_user_notification_email', array( 'Es_Auth', 'wrap_email' ) );

						remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
						add_action( 'register_new_user', array( 'Es_Auth', 'send_new_user_notifications' ) );

						do_action( 'register_new_user', $user_id );

						$login_page_id = ests( 'login_page_id' );

						es_set_flash( 'authenticate', __( 'You are successfully registered. Please check your email for account details.', 'es' ), 'success' );

						if ( $login_page_id && get_post_status( $login_page_id ) == 'publish' ) {
							wp_safe_redirect( add_query_arg( 'auth_item', 'login-form', get_permalink( $login_page_id ) ) );
							die;
						} else {
							wp_safe_redirect( es_get_success_auth_redirect_url() );
							die;
						}
					}
				}
			} else {
				es_set_flash( 'authenticate', __( 'Invalid reCAPTCHA. Please, reload the page and try again.', 'es' ), 'error' );

				wp_safe_redirect( es_get_auth_page_uri( "{$type}-register-form", $_SERVER['HTTP_REFERER'], $is_popup ) );
				die;
			}
		}
	}

	/**
	 * Login user using authenticate form.
	 *
	 * @return void
	 */
	public static function login() {
		$uniqid = es_post( 'uniqid' );
		if ( wp_verify_nonce( es_get_nonce( 'es_auth_nonce_' . $uniqid ), 'es_authenticate' ) ) {
			$is_popup = es_clean( filter_input( INPUT_POST, 'is_popup' ) );

			if ( es_verify_recaptcha() ) {
				$credentials = array(
					'user_login' => es_clean( filter_input( INPUT_POST, 'es_user_login' ) ),
					'user_password' => filter_input( INPUT_POST, 'es_user_password' ),
					'remember' => true,
				);

				$redirect_url = filter_input( INPUT_POST, 'redirect_url' );
				$user = wp_signon( $credentials, is_ssl() );

				if ( is_wp_error( $user ) ) {
					es_set_wp_error_flash( 'authenticate', $user );
					wp_safe_redirect( es_get_auth_page_uri( 'login-form', $_SERVER['HTTP_REFERER'], $is_popup ) );
					die;
				} else {
					wp_safe_redirect( $redirect_url ? add_query_arg( 'redirect_action', 'sign-in', $redirect_url ) : es_get_success_auth_redirect_url() );
					die;
				}
			} else {
				es_set_flash( 'authenticate', __( 'Invalid reCAPTCHA. Please, reload the page and try again.', 'es' ), 'error' );
				wp_safe_redirect( es_get_auth_page_uri( 'login-form', $_SERVER['HTTP_REFERER'], $is_popup ) );
				die;
			}
		}
	}

	/**
	 * Login user via social networks.
	 *
	 * @return void
	 * @throws Exception
	 */
	public static function social_login() {
		$auth_network = es_clean( filter_input( INPUT_GET, 'auth_network' ) );
		$networks = es_get_auth_networks_list();

		if ( stristr( $auth_network, 'login-buttons' ) ) {
			$network = str_replace( '-login-buttons', '', $auth_network );
			$_GET['context'] = 'login-buttons';

			if ( in_array( $network, $networks ) && ( $network = es_get_auth_instance( $network, es_clean( $_GET ) ) ) ) {
				try {
					$email = $network->get_user_email();
					$config = $network->get_config();

					if ( is_email( $email ) ) {
						if ( $user_id = email_exists( $email ) ) {
							wp_set_auth_cookie( $user_id, true, is_ssl() );
						} else {
							es_set_flash( 'authenticate', __( 'Account doesn\'t exist. Please, use sign up buttons instead.', 'es' ), 'error' );
							wp_safe_redirect( es_get_auth_page_uri( $config['context'], site_url() ) );
							die;
						}
					} else {
						es_set_flash( 'authenticate', __( 'Invalid user email.', 'es' ), 'error' );
						wp_safe_redirect( es_get_auth_page_uri( $config['context'], site_url() ) );
						die;
					}
				} catch ( Exception $e ) {
					es_set_flash( 'authenticate', $e->getMessage(), 'error' );
					wp_safe_redirect( es_get_auth_page_uri( 'login-buttons', site_url() ) );
					die;
				}
			} else {
				es_set_flash( 'authenticate', __( 'Invalid authentication method.', 'es' ), 'error' );
				wp_safe_redirect( es_get_auth_page_uri( 'login-buttons' ) );
				die;
			}
		} else if ( stristr( $auth_network, 'register-buttons' ) ) {
			foreach ( $networks as $network ) {
				if ( stristr( $auth_network, $network ) ) {
					$_GET['context'] = str_replace( $network . '-', '', $auth_network );
					$network = es_get_auth_instance( $network, es_clean( $_GET ) );
					$config = $network->get_config();

					try {
						$user_id = $network->register();

						if ( is_wp_error( $user_id ) ) {
							es_set_wp_error_flash( 'authenticate', $user_id );
							wp_safe_redirect( es_get_auth_page_uri( $config['context'], site_url() ) );
							die;
						} else {
							wp_set_auth_cookie( $user_id, true, is_ssl() );
							wp_safe_redirect( es_get_success_auth_redirect_url() );
							die;
						}
					} catch ( Exception $e ) {
						es_set_flash( 'authenticate', $e->getMessage(), 'error' );
						wp_safe_redirect( es_get_auth_page_uri( $config['context'], site_url() ) );
						die;
					}
				}
			}
		}

		wp_safe_redirect( es_get_success_auth_redirect_url() );
		die;
	}

	/**
	 * Retrieve reset password link.
	 *
	 * @return void
	 */
	public static function retrieve_password() {
		$uniqid = es_post( 'uniqid' );
		if ( wp_verify_nonce( es_get_nonce( 'es_retrieve_pwd_nonce_' . $uniqid ), 'es_retrieve_pwd' ) ) {
			$email = es_clean( filter_input( INPUT_POST, 'es_user_email' ) );
			$is_popup = es_clean( filter_input( INPUT_POST, 'is_popup' ) );

			if ( es_verify_recaptcha() ) {
				if ( $user_id = email_exists( $email ) ) {
					$user = get_user_by( 'id', $user_id );
					$_POST['user_login'] = $user->user_login;

					$result = static::retrieve_password_send();

					if ( is_wp_error( $result ) ) {
						es_set_wp_error_flash( 'authenticate', $result );
					} else {
						/* translators: %s: user email. */
						es_set_flash( 'authenticate', sprintf( __( 'A reset link is on its way. Please check your email inbox for %s and click the link to reset your password.', 'es' ), $user->user_email ) );
					}
				} else {
					es_set_flash( 'authenticate', __( 'User doesn\'t exist', 'es' ), 'error' );
				}
			} else {
				es_set_flash( 'authenticate', __( 'Invalid reCAPTCHA. Please, reload the page and try again.', 'es' ), 'error' );
			}

			$prev_url = esc_url( filter_input( INPUT_POST, '_wp_http_referer' ) );
			wp_safe_redirect( es_get_auth_page_uri( 'reset-form', $prev_url, $is_popup ) );
			die;
		}
	}

	/**
	 * Set new user password action.
	 *
	 * @return void
	 */
	public static function reset_password() {
		$uniqid = es_post( 'uniqid' );
		if ( wp_verify_nonce( es_get_nonce( 'es_reset_pwd_nonce_' . $uniqid ), 'es_reset_pwd' ) ) {
			$key = sanitize_text_field( filter_input( INPUT_POST, 'key' ) );
			$login = sanitize_text_field( filter_input( INPUT_POST, 'login' ) );
			$is_popup = es_clean( filter_input( INPUT_POST, 'is_popup' ) );

			if ( $key && $login ) {
				$user = check_password_reset_key( $key, $login );
				if ( is_wp_error( $user ) ) {
					es_set_wp_error_flash( 'authenticate', $user );
				} else {
					add_filter( 'wp_password_change_notification_email', array( 'Es_Auth', 'wrap_email' ) );
					reset_password( $user, sanitize_text_field( $_POST['es_new_password'] ) );
					es_set_flash( 'authenticate', __( 'Password successfully changed.', 'es' ), 'success' );
				}
			}

			$prev_url = esc_url( filter_input( INPUT_POST, '_wp_http_referer' ) );
			wp_safe_redirect( es_get_auth_page_uri( 'login-form', $prev_url, $is_popup ) );
			die;
		}
	}

	/**
	 * @param $user_id
	 * @param string $notify
	 */
	public static function send_new_user_notifications( $user_id, $notify = 'both' ) {
		$user = get_userdata( $user_id );

		if ( 'user' !== $notify ) {
			$switched_locale = switch_to_locale( get_locale() );

			es_send_email( 'new_user_registered_admin', get_option( 'admin_email' ), array(
				'user_login' => $user->user_login,
				'user_email' => $user->user_email,
				'user' => $user
			) );

			if ( $switched_locale ) {
				restore_previous_locale();
			}
		}

		// `$deprecated` was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
		if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
			return;
		}

		$key = get_password_reset_key( $user );
		if ( is_wp_error( $key ) ) {
			return;
		}

		$switched_locale = switch_to_locale( get_user_locale( $user ) );

		es_send_email( 'new_user_info', $user->user_email, array(
			'user_login' => $user->user_login,
			'user_email' => $user->user_email,
		) );

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}

	/**
	 * Handles sending password retrieval email to user.
	 *
	 * @since 2.5.0
	 *
	 * @return bool|WP_Error True: when finish. WP_Error on error
	 */
	public static function retrieve_password_send() {
		$errors = new WP_Error();

		if ( empty( $_POST['user_login'] ) || ! is_string( $_POST['user_login'] ) ) {
			$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Enter a username or email address.' ) );
		} elseif ( strpos( $_POST['user_login'], '@' ) ) {
			$user_data = get_user_by( 'email', trim( wp_unslash( $_POST['user_login'] ) ) );
			if ( empty( $user_data ) ) {
				$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: There is no account with that username or email address.' ) );
			}
		} else {
			$login     = trim( $_POST['user_login'] );
			$user_data = get_user_by( 'login', $login );
		}

		/**
		 * Fires before errors are returned from a password reset request.
		 *
		 * @since 2.1.0
		 * @since 4.4.0 Added the `$errors` parameter.
		 *
		 * @param WP_Error $errors A WP_Error object containing any errors generated
		 *                         by using invalid credentials.
		 */
		do_action( 'lostpassword_post', $errors );

		if ( $errors->has_errors() ) {
			return $errors;
		}

		if ( ! $user_data ) {
			$errors->add( 'invalidcombo', __( '<strong>ERROR</strong>: There is no account with that username or email address.' ) );
			return $errors;
		}

		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		$key        = get_password_reset_key( $user_data );

		if ( is_wp_error( $key ) ) {
			return $key;
		}

		$query_args = array(
			'auth_item' => 'reset-form',
			'key' => $key,
			'login' => rawurlencode( $user_login ),
		);

		$login_page_id = ests( 'login_page_id' );

		if ( $login_page_id && get_post_status( $login_page_id ) == 'publish' ) {
			$link = add_query_arg( $query_args, get_permalink( $login_page_id ) );
		} else {
			$link = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );
		}

		$email_instance = es_get_email_instance( 'reset_password', array(
			'reset_link' => $link,
			'user_login' => $user_login,
			'user_email' => $user_email,
		) );

		if ( ! $email_instance::is_active() || ! $email_instance->send( $user_email ) ) {
			$errors->add(
				'retrieve_password_email_failure',
				sprintf(
				/* translators: %s: Documentation URL. */
					__( '<strong>ERROR</strong>: The email could not be sent. Your site may not be correctly configured to send emails. <a href="%s">Get support for resetting your password</a>.' ),
					esc_url( __( 'https://wordpress.org/support/article/resetting-your-password/' ) )
				)
			);
			return $errors;
		}

		return true;
	}
}

Es_Auth::init();
