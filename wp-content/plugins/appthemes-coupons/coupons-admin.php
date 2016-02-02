<?php

if ( is_admin() ) {
	add_filter( 'manage_' . APPTHEMES_COUPON_PTYPE . '_posts_columns', 'appthemes_coupon_manage_columns' );
	add_action( 'manage_' . APPTHEMES_COUPON_PTYPE . '_posts_custom_column', 'appthemes_coupon_add_column_data', 10, 2 );
	add_filter( 'the_title', 'appthemes_coupon_modify_title', 10, 2 );
	add_action( 'admin_print_styles', 'appthemes_coupons_icon' );
}


/**
 * Modifies columns on admin coupons page.
 *
 * @param array $columns
 *
 * @return array
 */
function appthemes_coupon_manage_columns( $columns ) {

	$columns['title'] = __( 'Coupon Code', 'appthemes-coupons' );
	$columns['discount'] = __( 'Discount', 'appthemes-coupons' );
	$columns['usage'] = __( 'Usage', 'appthemes-coupons' );
	$columns['expires'] = __( 'Expires', 'appthemes-coupons' );
	$columns['status'] = __( 'Status', 'appthemes-coupons' );

	unset( $columns['date'] );

	return $columns;
}


/**
 * Displays coupon custom columns data.
 *
 * @param string $column_index
 * @param int $post_id
 *
 * @return void
 */
function appthemes_coupon_add_column_data( $column_index, $post_id ) {

	$coupon = get_post( $post_id );
	$coupon_meta = get_post_custom( $post_id );

	switch ( $column_index ) {

		case 'discount' :
			if ( $coupon_meta['type'][0] == 'flat' ) {
				$discount = (float) $coupon_meta['amount'][0];
				appthemes_display_price( $discount );
			} else {
				$discount = (int) $coupon_meta['amount'][0];
				echo $discount . '%';
			}
			break;

		case 'usage' :
			$uses = ( isset( $coupon_meta['use_count'][0] ) ) ? $coupon_meta['use_count'][0] : 0;
			if ( empty( $coupon_meta['use_limit'][0] ) ) {
				printf( __( '%d / Unlimited', 'appthemes-coupons' ), $uses );
			} else {
				printf( _x( '%1$d / %2$d', 'Coupon usage, 1 - uses, 2 - limit', 'appthemes-coupons' ), $uses, $coupon_meta['use_limit'][0] );
			}
			break;

		case 'expires' :
			if ( empty( $coupon_meta['end_date'][0] ) ) {
				_e( 'Not expiring', 'appthemes-coupons' );
			} else {
				echo appthemes_display_date( $coupon_meta['end_date'][0], 'date' );
			}
			break;

		case 'status' :
			if ( $coupon->post_status == 'publish' ) {
				_e( 'Active', 'appthemes-coupons' );
			} else {
				_e( 'Inactive', 'appthemes-coupons' );
			}
			break;

	}
}


/**
 * Midifies coupon title.
 *
 * @param string $title
 * @param int $post_id
 *
 * @return string
 */
function appthemes_coupon_modify_title( $title, $post_id ) {

	$post = get_post( $post_id );
	if ( $post->post_type != APPTHEMES_COUPON_PTYPE ) {
		return $title;
	}

	return get_post_meta( $post_id, 'code', true );
}


/**
 * Outputs styles for admin coupons icon.
 *
 * @return void
 */
function appthemes_coupons_icon() {
	$url = plugins_url( '/css/images/coupons-med.png', __FILE__ );
	echo <<<EOB
<style type="text/css">
	.icon32-posts-discount_coupon{
		background-image: url($url);
		background-position: -2px -5px !important;
	}
</style>
EOB;
}
