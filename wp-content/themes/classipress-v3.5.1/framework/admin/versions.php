<?php
/**
 * Versions
 *
 * @package Framework\Versions
 */

add_action( 'admin_init', 'appthemes_update_redirect' );
add_action( 'appthemes_first_run', 'appthemes_updated_version_notice', 999 );


define( 'APP_UPDATE_TRANSIENT', 'app_update_version' );

function appthemes_update_redirect() {
	if ( ! current_user_can( 'manage_options' ) || defined( 'DOING_AJAX' ) ) {
		return;
	}

	// numeric array, contains multiple sets of arguments
	// first item contains preferable set
	$args_sets = get_theme_support( 'app-versions' );
	$redirect  = false;

	foreach ( $args_sets as $args ) {
		if ( $args['current_version'] == get_option( $args['option_key'] ) ) {
			continue;
		}

		if ( $args['current_version'] == get_transient( APP_UPDATE_TRANSIENT . '_' . $args['option_key'] ) ) {
			continue;
		}

		set_transient( APP_UPDATE_TRANSIENT. '_' . $args['option_key'], $args['current_version'] );

		// set redirect only for the first available arg set
		if ( ! $redirect ) {
			$redirect = $args['update_page'];
		}
	}

	// no any redirect in all arg sets
	if ( ! $redirect ) {
		return;
	}

	// prevents infinite redirect
	if ( scbUtil::get_current_url() == admin_url( $redirect ) ) {
		return;
	}

	wp_redirect( admin_url( $redirect ) );
	exit;
}

function appthemes_updated_version_notice() {
	$args_sets = get_theme_support( 'app-versions' );

	foreach ( $args_sets as $args ) {
		if ( $args['current_version'] != get_transient( APP_UPDATE_TRANSIENT . '_' . $args['option_key'] ) ) {
			continue;
		}

		$option_key  = $args['option_key'];
		$new_version = $args['current_version'];
		$old_version = get_option( $option_key );

		// add a hook here to avoid duplicated versions checks in upgrade procedures
		do_action( "appthemes_upgrade_$option_key", $new_version, $old_version );

		update_option( $option_key, $new_version );

		$app_name = ( isset( $args['app_name'] ) ) ? $args['app_name'] : __( 'Theme', APP_TD );
		echo scb_admin_notice( sprintf(
			__( '%1$s successfully updated to version %2$s.', APP_TD ),
			$app_name, $new_version
		) );

		delete_transient( APP_UPDATE_TRANSIENT . '_' . $option_key );
	}
}