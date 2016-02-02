<?php
/*
Plugin Name: AppThemes Coupons
Description: Creates and allows coupon codes to be used during the checkout process of AppThemes Products.

AppThemes ID: appthemes-coupons

Version: 1.1
Author: AppThemes
Author URI: http://appthemes.com
Text Domain: appthemes-coupons
*/

define( 'APPTHEMES_COUPON_PTYPE', 'discount_coupon' );

add_action( 'init', 'appthemes_coupons_setup' );
add_action( 'appthemes_first_run', 'appthemes_coupons_upgrade' );

$locale = apply_filters( 'plugin_locale', get_locale(), 'appthemes-coupons' );
load_textdomain( 'appthemes-coupons', WP_LANG_DIR . "/plugins/coupons-$locale.mo" );


/**
 * Setups coupons.
 *
 * @return void
 */
function appthemes_coupons_setup() {

	// Check for right version of Theme
	if ( ! class_exists( 'APP_Item_Registry' ) ) {
		if ( ! appthemes_coupon_is_network_activated() ) {
			add_action( 'admin_notices', 'appthemes_coupon_display_version_warning' );
		}
		return;
	}

	if ( is_admin() ) {

		if ( ! class_exists( 'APP_Item_Registry' ) ) {
			add_action( 'admin_notices', 'appthemes_coupon_display_version_warning' );
			return;
		}

		require_once( dirname(__FILE__) . '/coupons-meta-class.php' );
		require_once( dirname(__FILE__) . '/coupons-admin.php' );

		new APP_Coupon_Details();
	}

	appthemes_coupons_register_post_type();

	if ( defined( 'VA_VERSION' ) && version_compare( VA_VERSION, '1.2', '<' ) ) {
		// Register Action Hooks for Vantage less than 1.2
		add_action( 'va_after_purchase_listing_new_form', 'appthemes_coupon_add_field' );
		add_filter( 'va_listing_validate_purchase_fields', 'appthemes_coupons_validate_field' );
		add_action( 'va_create_listing_order', 'appthemes_coupon_add_coupon', 11 );
	} else {
		// Register Action Hooks
		add_action( 'appthemes_purchase_fields', 'appthemes_coupon_add_field' );
		add_filter( 'appthemes_validate_purchase_fields', 'appthemes_coupons_validate_field' );
		add_action( 'appthemes_create_order', 'appthemes_coupon_add_coupon', 11 );
	}

	add_filter( 'appthemes_order_item_posts_types', 'appthemes_coupon_add_ptype' );
	add_action( 'admin_menu', 'appthemes_coupons_add_menu', 11 );

	add_filter( 'post_updated_messages', 'appthemes_coupons_update_messages' );

	APP_Item_Registry::register( APPTHEMES_COUPON_PTYPE, new APP_JIT_Text( 'appthemes_coupon_text' ) );

}


/**
 * Coupons upgrade script for Vantage theme.
 *
 * @return void
 */
function appthemes_coupons_upgrade() {
	if ( ! defined( 'VA_VERSION' ) ) {
		return;
	}

	$posts = new WP_Query( array(
		'post_type' => 'coupon',
		'post_status' => 'any',
		'nopaging' => true
	) );

	foreach ( $posts->posts as $post ) {

		$code = get_post_meta( $post->ID, 'code', true );
		if ( empty( $code ) ) {
			continue;
		}

		wp_update_post( array(
			'ID' => $post->ID,
			'post_type' => APPTHEMES_COUPON_PTYPE
		) );
	}

}


/**
 * Registers custom post type for coupons.
 *
 * @return void
 */
function appthemes_coupons_register_post_type() {

	$labels = array(
		'name' => __( 'Coupons', 'appthemes-coupons' ),
		'singular_name' => __( 'Coupons', 'appthemes-coupons' ),
		'add_new' => __( 'Add New', 'appthemes-coupons' ),
		'add_new_item' => __( 'Add New Coupon', 'appthemes-coupons' ),
		'edit_item' => __( 'Edit Coupon', 'appthemes-coupons' ),
		'new_item' => __( 'New Coupon', 'appthemes-coupons' ),
		'view_item' => __( 'View Coupon', 'appthemes-coupons' ),
		'search_items' => __( 'Search Coupons', 'appthemes-coupons' ),
		'not_found' => __( 'No coupons found', 'appthemes-coupons' ),
		'not_found_in_trash' => __( 'No coupons found in Trash', 'appthemes-coupons' ),
		'parent_item_colon' => __( 'Parent Coupon:', 'appthemes-coupons' ),
		'menu_name' => __( 'Coupons', 'appthemes-coupons' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'supports' => array( 'no-ops' ),
		'public' => false,
		'capability_type' => 'page',
		'show_ui' => true,
		'show_in_menu' => false,
	);

	// Allow themes to modify post type arguments
	$args = apply_filters( 'appthemes_coupon_ptype_args', $args );

	register_post_type( APPTHEMES_COUPON_PTYPE, $args );

}


/**
 * Adds Coupons to Payments menu.
 *
 * @return void
 */
function appthemes_coupons_add_menu() {
	$ptype = APPTHEMES_COUPON_PTYPE;
	$ptype_obj = get_post_type_object( $ptype );
	add_submenu_page( 'app-payments', $ptype_obj->labels->name, $ptype_obj->labels->all_items, $ptype_obj->cap->edit_posts, "edit.php?post_type=$ptype" );
}


/**
 * Modifies messages on admin edit coupon page.
 *
 * @param array $messages
 *
 * @return array
 */
function appthemes_coupons_update_messages( $messages ) {
	$messages[ APPTHEMES_COUPON_PTYPE ] = array(
	 	1 => __( 'Coupon updated.', 'appthemes-coupons' ),
	 	4 => __( 'Coupon updated.', 'appthemes-coupons' ),
	 	6 => __( 'Coupon created.', 'appthemes-coupons' ),
	 	7 => __( 'Coupon saved.', 'appthemes-coupons' ),
	 	9 => __( 'Coupon scheduled.', 'appthemes-coupons' ),
		10 => __( 'Coupon draft updated.', 'appthemes-coupons' ),
	);
	return $messages;
}


/**
 * Returns coupon code text.
 *
 * @return string
 */
function appthemes_coupon_text() {

	$post = get_queried_object();
	if ( ! $post ) {
		if ( isset( $_GET['post'] ) ) {
			$post = get_post( $_GET['post'] );
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post = get_post( $_POST['post_ID'] );
		}
	}

	if ( ! is_object( $post ) || is_wp_error( $post ) ) {
		return __( 'Coupon Code', APP_TD );
	}

	$order = appthemes_get_order( $post->ID );
	if ( ! $order ) {
		return __( 'Coupon Code', APP_TD );
	}

	$coupons = $order->get_items( APPTHEMES_COUPON_PTYPE );
	$coupon = $coupons[0];

	$coupon_code = get_post_meta( $coupon['post_id'], 'code', true );

	return sprintf( __( 'Coupon Code: "%s"', 'appthemes-coupons' ), $coupon_code );
}


/**
 * Adds coupons post type to supported order items.
 *
 * @param array $post_types
 *
 * @return void
 */
function appthemes_coupon_add_ptype( $post_types ) {
	$post_types[] = APPTHEMES_COUPON_PTYPE;
	return $post_types;
}


/**
 * Displays coupon code form field.
 *
 * @return void
 */
function appthemes_coupon_add_field() {
?>
<fieldset id="coupon-fields">
	<div class="featured-head"><h3><?php _e( 'Coupons', 'appthemes-coupons' ); ?></h3></div>

	<div class="form-field coupon-code"><label>
		<?php _e( 'Have a Coupon Code? Enter it here.', 'appthemes-coupons' ); ?>
		<input name="coupon-code" type="text" value="" />
	</label></div>

</fieldset>
<?php
}


/**
 * Validates submitted coupon code.
 *
 * @param object $errors
 *
 * @return object
 */
function appthemes_coupons_validate_field( $errors ) {

	if ( empty( $_POST['coupon-code'] ) ) {
		return $errors;
	}

	$posts = new WP_Query( array(
		'post_type' => APPTHEMES_COUPON_PTYPE,
		'meta_key' => 'code',
		'meta_value' => $_POST['coupon-code']
	) );

	if ( $posts->post_count == 0 ) {
		$errors->add( 'invalid-coupon', __( 'Entered Coupon Code is invalid.', 'appthemes-coupons' ) );
		return $errors;
	}

	$coupon_data = get_post_custom( $posts->post->ID );
	if ( ! empty( $coupon_data['start_date'][0] ) && strtotime( $coupon_data['start_date'][0]) > time() ) {
		$errors->add( 'invalid-coupon', __( 'Entered Coupon Code is invalid.', 'appthemes-coupons' ) );
		return $errors;
	}

	if ( ! empty( $coupon_data['end_date'][0] ) && strtotime( $coupon_data['end_date'][0]) < time() ) {
		$errors->add( 'invalid-coupon', __( 'Entered Coupon Code is invalid.', 'appthemes-coupons' ) );
		return $errors;
	}

	if ( ! empty( $coupon_data['use_limit'][0] ) && $coupon_data['use_limit'][0] > 0 ) {

		$uses = $coupon_data['use_count'][0];
		if ( $uses && $uses >= $coupon_data['use_limit'][0] ) {
			$errors->add( 'invalid-coupon', __( 'This coupon has been used too many times.', 'appthemes-coupons' ) );
			return $errors;
		}

	}

	if ( ! empty( $coupon_data['user_use_limit'][0] ) && $coupon_data['user_use_limit'][0] > 0 ) {

		$current_user = wp_get_current_user();
		$uses = get_user_meta( $current_user->ID, $_POST['coupon-code'], true );
		if ( $uses && $uses >= $coupon_data['user_use_limit'][0] ) {
			$errors->add( 'invalid-coupon', __( 'You have used this coupon too many times..', 'appthemes-coupons' ) );
			return $errors;
		}

	}

	return $errors;
}


/**
 * Adds a coupon to order.
 *
 * @param object $order
 *
 * @return void
 */
function appthemes_coupon_add_coupon( $order ) {

	if ( empty( $_POST['coupon-code'] ) ) {
		return;
	}

	$coupon_code = $_POST['coupon-code'];
	$posts = new WP_Query( array(
		'post_type' => APPTHEMES_COUPON_PTYPE,
		'meta_key' => 'code',
		'meta_value' => $coupon_code
	) );

	$coupon = $posts->post;
	$coupon_data = get_post_custom( $coupon->ID );
	$discount = 0;

	$uses = ( isset( $coupon_data['use_count'][0] ) ) ? $coupon_data['use_count'][0] + 1 : 1;

	if ( $order->get_items( APPTHEMES_COUPON_PTYPE ) ) {
		$order->remove_item( APPTHEMES_COUPON_PTYPE );
		$uses--;
	}

	switch( $coupon_data['type'][0] ) {

		case 'flat':
			$discount = (float) $coupon_data['amount'][0];
			break;
		case 'percent':
			$multiplier = ( (int) $coupon_data['amount'][0] ) / 100;
			$discount = $order->get_total() * $multiplier;
			break;

	}

	$order->add_item( APPTHEMES_COUPON_PTYPE, number_format( -1 * $discount, 2, '.', '' ), $coupon->ID, true );

	if ( ! empty( $coupon_data['user_use_limit'][0] ) && $coupon_data['user_use_limit'][0] > 0 ) {

		$current_user = wp_get_current_user();
		$user_uses = get_user_meta( $current_user->ID, $_POST['coupon-code'], true );

		$user_uses = $user_uses + 1;
		update_user_meta( $current_user->ID, $_POST['coupon-code'], $user_uses );

	}

	update_post_meta( $coupon->ID, 'use_count', $uses );
}


/**
 * Displays version warning and deactivates plugin.
 *
 * @return void
 */
function appthemes_coupon_display_version_warning() {

	$message = __( 'AppThemes Coupons could not run.', 'appthemes-coupons' );

	if ( ! current_theme_supports( 'app-payments' ) ) {
		$message = __( 'AppThemes Coupons does not support the current theme. Please use a compatible AppThemes Product.', 'appthemes-coupons' );
	}

	echo '<div class="error fade"><p>' . $message . '</p></div>';
	deactivate_plugins( plugin_basename( __FILE__ ) );
}


/**
 * Checks if plugin is network activated.
 *
 * @return bool
 */
function appthemes_coupon_is_network_activated() {
	if ( ! is_multisite() ) {
		return false;
	}

	$plugins = get_site_option( 'active_sitewide_plugins' );

	return isset( $plugins[ plugin_basename( __FILE__ ) ] );
}


class APP_JIT_Text{

	private $callback;

	public function __construct( $callback ) {
		$this->callback = $callback;
	}

	public function __toString() {
		return (string) call_user_func( $this->callback );
	}

}

