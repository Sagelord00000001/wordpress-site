<?php

/**
 * Class Es_Search_Form_Shortcode.
 */
class Es_Request_Form_Shortcode extends Es_Shortcode {

    /**
     * @var int
     */
    const SEND_ADMIN = 1;

    /**
     * @var int
     */
    const SEND_OTHER = -1;

    /**
     * Return list of "send_to" selectbox field.
     *
     * @return mixed
     */
    public static function get_send_to_list() {
        return apply_filters( 'es_request_widget_get_send_to_list', array(
            self::SEND_ADMIN       => __( 'Admin', 'es' ),
            self::SEND_OTHER       => __( 'Other email', 'es' ),
        ) );
    }

    /**
     * Return search shortcode DOM.
     *
     * @return string|void
     */
    public function get_content() {
        ob_start();
        es_load_template( sprintf( 'front/shortcodes/request/request-form-%s.php', $this->_attributes['layout'] ), array(
            'shortcode_instance' => $this,
            'attributes' => $this->_attributes,
        ) );
        return ob_get_clean();
    }

    /**
     * Return shortcode default attributes.
     *
     * @return array|void
     */
    public function get_default_attributes() {
        return apply_filters( sprintf( '%s_default_attributes', static::get_shortcode_name() ), array(
            'layout' => 'sidebar', // sidebar, section
            'title' => __( 'Ask an Agent About This Home', 'es' ),
            'background' => '#263238',
            'color' => '#ffffff',
            'post_id' => get_the_ID(),
            'recipient_type' => static::SEND_ADMIN,
            'name' => null,
            'email' => null,

            'message' => __( "Hello, I'd like more information about this home. Thank you!", 'es' ),
            'disable_tel' => false,
            'disable_name' => false,
            'custom_email' => false,
            'disable_email' => false,
            'subject' => __( 'Estatik Request Info from', 'es' ),
            'button_text' => __( 'Request info', 'es' ),
        ) );
    }

    /**
     * @return Exception|string
     */
    public static function get_shortcode_name() {
        return 'es_request_form';
    }

    /**
     * Return request form frontend fields config.
     *
     * @return mixed|void
     */
    public function get_fields_config() {
        $uid = uniqid();
        $user = es_get_user_entity();

        return apply_filters( 'es_request_form_fields', array(
            'name' => array(
                'label' => __( 'Name*', 'es' ),
                'type' => 'text',
                'attributes' => array(
                    'required' => 'required',
                    'id' => 'name-' . $uid,
	                'maxlength' => 50,
                ),
	            'value' => $user ? $user->get_full_name() : '',
            ),
            'email' => array(
                'label' => __( 'Email*', 'es' ),
                'type' => 'email',
                'attributes' => array(
                    'required' => 'required',
                    'id' => 'email-' . $uid,
                    'maxlength' => 256,
                ),
	            'value' => $user ? $user->get_email() : '',
            ),
            'phone' => array(
	            'label' => __( 'Phone', 'es' ),
	            'type' => 'phone',
	            'is_country_code_disabled' => ests( 'is_tel_code_disabled' ),
	            'codes' => es_esc_json_attr( ests_values( 'phone_codes' ) ),
	            'icons' => es_esc_json_attr( ests_values( 'country_icons' ) ),
	            'code_config' => array(
	            	'options' => ests_values( 'country' ),
		            'attributes' => array(
		            	'id' => 'es-field-code-' . uniqid(),
		            )
	            ),
            ),
            'message' => array(
                'label' => __( 'Message*', 'es' ),
                'type' => 'textarea',
                'attributes' => array(
                    'id' => 'message-' . $uid,
	                'required' => 'required',
	                'maxlength' => 500,
                ),
	            'value' => $this->_attributes['message']
            ),
        ), $this );
    }

    /**
     * Submit request form handler.
     *
     * @return void
     */
    public static function submit_form() {
        $btn = '<a href="" class="es-btn es-btn--secondary js-es-close-popup">' . __( 'Got it', 'es' ) . '</a>';
        $id = es_clean( filter_input( INPUT_POST, 'uniqid' ) );
        if ( wp_verify_nonce( es_get_nonce( 'es_request_form_nonce_' . $id ), 'es_submit_request_form' ) ) {
            if ( es_verify_recaptcha() ) {
                if ( ( isset( $_POST['terms_conditions'] ) && ! empty( $_POST['terms_conditions'] ) ) || ! isset( $_POST['terms_conditions'] ) ) {
                    $data = apply_filters( 'es_request_form_submit_data', es_clean( $_POST ) );
                    $instance = new static( $data );
	                $data['subject'] = $instance->_attributes['subject'];
                    $emails = $instance->get_emails();

					$email_instance = es_get_email_instance( static::get_email_instance_name( $data ), $data );

                    if ( $email_instance::is_active() && $emails && $email_instance->send( $emails ) ) {
                        $response = es_success_ajax_response(
                            __( '<span class="es-icon es-icon_check-mark"></span><h4>Thank you!</h4><p>We\'ve sent along your message. The agent will follow up with you soon.</p>', 'es' ) . $btn );
	                    do_action( 'es_after_request_form_submitted', $data, $emails );
                    } else {
                        $response = es_error_ajax_response(
                            __( '<span class="es-icon es-icon_close"></span><h4>Error!</h4><p>Your message wasn\'t sent. Please, contact support.</p>', 'es' ) . $btn );
                    }
                } else {
                    $response = es_error_ajax_response(
                        __( '<span class="es-icon es-icon_close"></span><h4>Error!</h4><p>Please, confirm terms & conditions</p>', 'es' ) . $btn );
                }
            } else {
                $response = es_error_ajax_response(
                    __( '<span class="es-icon es-icon_close"></span><h4>Error!</h4><p>Invalid reCAPTCHA. Please, reload the page and try again.</p>', 'es' ) . $btn );
            }

        } else {
            $error = __( 'Invalid security nonce. Please, reload the page and try again.', 'es' );
            $response = es_error_ajax_response( sprintf( '<span class="es-icon es-icon_close"></span><h4>%s</h4><p>%s</p>%s', __( 'Error!', 'es' ), $error, $btn ) );
        }

        $content = $response;
        $response['message'] = sprintf( "<div id='es-request-form-popup' class='es-magnific-popup es-ajax-form-popup'>%s</div>", $response['message'] );

        wp_die( json_encode( apply_filters( 'es_request_form_submit_response', $response, $content ) ) );
    }

	/**
	 * @param $data
	 *
	 * @return string
	 */
	public static function get_email_instance_name( $data ) {
		$result = 'request_property_info';

		if ( ! empty( $data['post_id'] ) ) {
			$entity = es_get_entity_by_id( $data['post_id'] );
			if ( $entity ) {
				$email_instance = sprintf( 'request_%s_info', $entity::get_entity_name() );
				$types = es_get_email_types_list();

				$result = ! empty( $types[ $email_instance ] ) ? $email_instance : $result;
			}
		}

		return $result;
	}

    /**
     * Get recipient emails by recipient type.
     *
     * @return mixed|void
     */
    public function get_emails() {
        $emails = array();
        $type = $this->_attributes['recipient_type'];

        if ( static::SEND_OTHER == $type && ( $another_emails = filter_input( INPUT_POST, 'send_to_emails' ) ) ) {
            $another_emails = explode( ',', $another_emails );

            if ( $another_emails ) {
                foreach ( $another_emails as $email ) {
                    if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                        $emails[] = $email;
                    }
                }
            }
        }

        if ( static::SEND_ADMIN == $type ) {
            $emails = es_get_admin_emails();
        }

        $emails = $emails ? array_unique( $emails ) : array();

        return apply_filters( 'es_request_form_get_emails', $emails, $this );
    }
}

add_action( 'wp_ajax_es_submit_request_form', array( 'Es_Request_Form_Shortcode', 'submit_form' ) );
add_action( 'wp_ajax_nopriv_es_submit_request_form', array( 'Es_Request_Form_Shortcode', 'submit_form' ) );
