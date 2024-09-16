<?php

/**
 * Class Es_Estatik_Logo
 */
class Es_Estatik_Logo {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'es_logo', arraY( 'Es_Estatik_Logo', 'render' ) );
	}

	/**
	 * Return estatik logo.
	 *
	 * @return void
	 */
	public static function render() {
		echo sprintf( "<img class='es-logo' src='%s'><span>%s</span>", static::get_url(), sprintf( __( 'Version %s', 'es' ), Estatik::get_version() ) );
	}

	/**
	 * Return logo URL.
	 *
	 * @return string
	 */
	public static function get_url() {
		return apply_filters( 'es_logo_url', ES_PLUGIN_URL . 'admin/images/logo-ver.svg' );
	}
}

Es_Estatik_Logo::init();
