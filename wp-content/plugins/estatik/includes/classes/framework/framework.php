<?php

if ( ! defined( 'DS' ) ) {
	define( 'DS', '/' );
}

require_once 'functions.php';
require_once 'class-estatik-framework.php';
require_once 'ajax.php';

/**
 * Return framework instance.
 *
 * @return Es_Framework
 */
function es_framework_instance( $config = array() ) {
	return apply_filters( 'es_framework_instance', Es_Framework::get_instance( $config ) );
}

/**
 * Get view instance.
 *
 * @param string $view
 * @param array $config
 *
 * @return Es_Framework_View
 */
function es_framework_get_view( $view, $config = array() ) {
	$framework = es_framework_instance();
	$factory = $framework->views_factory();

	return $factory::get_view_instance( $view, $config );
}

/**
 * Render viw element.
 *
 * @param string $view
 * @param array $config
 */
function es_framework_view_render( $view, $config = array() ) {
	if ( $view = es_framework_get_view( $view, $config ) ) {
		$view->render();
	}
}

/**
 * Return field instance.
 *
 * @param $field_key
 * @param $field_config
 *
 * @return Es_Framework_Base_Field
 */
function es_framework_get_field( $field_key, $field_config ) {
	$framework = es_framework_instance();
	$factory = $framework->fields_factory();

	return $factory::get_field_instance( $field_key, $field_config );
}

/**
 * @param $field_key
 * @param $field_config
 *
 * @return string
 */
function es_framework_get_field_html( $field_key, $field_config ) {
    $field = es_framework_get_field( $field_key, $field_config );

    return $field->get_markup();
}

/**
 * Render field handler.
 *
 * @param $field_key
 * @param $field_config
 *
 * @return void
 */
function es_framework_field_render( $field_key, $field_config ) {
	$field = es_framework_get_field( $field_key, $field_config );
	$field->render();
}
