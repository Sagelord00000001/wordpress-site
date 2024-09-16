<?php

/**
 * Class Es_Email.
 */
abstract class Es_Email {

	/**
	 * List of emails recipients.
	 *
	 * @var array|string
	 */
	protected $_to;

	/**
	 * Return email content.
	 *
	 * @return string
	 */
	abstract public function get_content();

	/**
	 * Return email subject.
	 *
	 * @return string
	 */
	abstract public function get_subject();

	/**
	 * Name of email in admin panel.
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function get_label() {
		throw new Exception( 'Need to set email label using get_label static method.' );
	}

	/**
	 * Email data.
	 *
	 * @var array
	 */
	protected $_data;
	protected $_headers = array();

	/**
	 * Es_Email constructor.
	 *
	 * @param array $data
	 */
	public function __construct( $data = array() ) {
		$this->_data = $data;
	}

	/**
	 * @return mixed|void
	 */
	public function get_tokens() {
		return apply_filters( 'es_email_default_tokens', array(
			'{site_name}' => get_bloginfo( 'name' ),
			'{site_url}' => get_bloginfo( 'url' ),
			'{profile_link}' => es_get_page_url( 'profile' ),
			'{reset_password_link}' => es_get_auth_page_uri( 'reset-form', es_get_page_url( 'reset_password' ) ),
		), $this );
	}

	/**
	 * Wrap email content with email template.
	 *
	 * @return false|string
	 */
	public function generate_message() {
		ob_start();
		$tokens = apply_filters( 'es_email_tokens', $this->get_tokens(), $this );
		$content = stripslashes( strtr( $this->get_content(), $tokens ) );
		$content = apply_filters( 'es_email_message_content', wpautop( $content, false ), $content, $this );
		es_load_template( 'common/emails/template.php', array( 'content' => $content ) );
		$final_content = ob_get_clean();

		return apply_filters( 'es_email_message', $final_content, $this );
	}

	/**
	 * @return array
	 */
	public function get_data() {
		return $this->_data;
	}

	/**
	 * Return email headers.
	 *
	 * @return string[]
	 */
	public function get_headers() {
		return apply_filters( 'es_email_headers', array_merge( array(
			'Content-Type: text/html',
		), $this->_headers ), $this );
	}

	/**
	 * @param $headers
	 */
	public function set_headers( $headers ) {
		$this->_headers = $headers;
	}

	/**
	 * Send email handler.
	 *
	 * @param string $to
	 *
	 * @return bool
	 */
	public function send( $to = '' ) {
		$subject = strtr( $this->get_subject(), $this->get_tokens() );
		do_action( 'es_email_before_send', $this );
		$to = $to ? $to : $this->_to;
		$to = apply_filters( 'es_email_to', $to, $this );
		$subject = stripslashes( $subject );
		return wp_mail( $to, $subject, $this->generate_message(), $this->get_headers() );
	}

	/**
	 * @return false
	 */
	public static function is_disableable() {
		return apply_filters( 'es_email_is_disableable', false, get_called_class() );
	}

	/**
	 * @return bool
	 */
	public static function is_active() {
		return apply_filters( 'es_email_is_active', true, get_called_class() );
	}
}
