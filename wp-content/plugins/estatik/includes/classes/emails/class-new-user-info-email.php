<?php

class Es_New_User_Info_Email extends Es_Email {

	/**
	 * @return mixed|string[]|void
	 */
	public function get_tokens() {
		$d = $this->get_data();

		return array_merge( parent::get_tokens(), array(
			'{user_login}' => $d['user_login'],
			'{user_email}' => $d['user_email'],
		) );
	}

	public function get_content() {
		$content = ests( 'new_user_info_email_content' );
		return apply_filters( 'es_new_user_info_email_content', $content );
	}

	public function get_subject() {
		$subject = ests( 'new_user_info_email_subject' );
		return apply_filters( 'es_new_user_info_email_subject', $subject );
	}

	public static function get_label() {
		return __( 'New user info after registration', 'es' );
	}
}
