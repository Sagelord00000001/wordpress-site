<?php

class Es_Reset_Password_Email extends Es_Email {

	/**
	 * @return mixed|string[]|void
	 */
	public function get_tokens() {
		$d = $this->get_data();

		return array_merge( parent::get_tokens(), array(
			'{user_login}' => $d['user_login'],
			'{user_email}' => $d['user_email'],
			'{reset_link}' => $d['reset_link'],
		) );
	}

	/**
	 * @return mixed|string|void
	 */
	public function get_content() {
		$content = ests( 'reset_password_email_content' );
		return apply_filters( 'es_reset_password_email_content', $content );
	}

	/**
	 * @return mixed|string|void
	 */
	public function get_subject() {
		$subject = ests( 'reset_password_email_subject' );
		return apply_filters( 'es_reset_password_email_subject', $subject );
	}

	public static function get_label() {
		return __( 'Reset password', 'es' );
	}
}
