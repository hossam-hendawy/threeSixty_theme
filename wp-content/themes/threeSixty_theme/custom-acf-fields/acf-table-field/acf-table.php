<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads plugin textdomain.
 * https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
 */

function acf_table_load_plugin_textdomain( $version ) {

	load_plugin_textdomain( 'acf-table', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

add_action( 'plugins_loaded', 'acf_table_load_plugin_textdomain' );


/**
 * Registers the ACF field type.
 */

add_action( 'init', 'jh_include_acf_field_table' );


function jh_include_acf_field_table() {

	if ( ! function_exists( 'acf_register_field_type' ) ) {

		return;
	}

	require_once __DIR__ . '/class-jh-acf-field-table.php';

	acf_register_field_type( 'jh_acf_field_table' );
}
