<?php
/**
 * Coupon Details Metabox.
 */
class APP_Coupon_Details extends APP_Meta_Box {

	public function __construct() {
		parent::__construct( 'coupon-details', __( 'Coupon Details', 'appthemes-coupons' ), APPTHEMES_COUPON_PTYPE, 'normal', 'high' );
	}

	public function admin_enqueue_scripts() {
		if ( is_admin() ) {
			wp_enqueue_style( 'jquery-ui-datepicker', plugins_url( 'css/jquery-ui.css', __FILE__ ) );
			wp_enqueue_script( 'jquery-ui-datepicker' );
		}
	}

	protected function validate_post_data( $post_data, $post_id ) {

		if ( ! empty( $post_data['start_date'] ) ) {
			$date = date_parse( $post_data['start_date'] );
			if ( ! checkdate( $date['month'], $date['day'], $date['year'] ) || ! empty( $date['errors'] ) ) {
				return new WP_Error( 'start_date' );
			}
		}

		if ( ! empty( $post_data['end_date'] ) ) {
			$date = date_parse( $post_data['end_date'] );
			if ( ! checkdate( $date['month'], $date['day'], $date['year'] ) || ! empty( $date['errors'] ) ) {
				return new WP_Error( 'end_date' );
			}
		}

	}

	public function before_form( $post ) {
		?>
		<script type="text/javascript">
		jQuery(function($) {
			$( "#start_date" ).datepicker();
			$( "#end_date" ).datepicker();
		});
		</script>
		<?php
	}

	public function form() {

		// translators: Start/End Date Help Text. Can be any format accepted by php.net/strtotime and php.net/date_parse.
		$date_format = __( '( MM/DD/YYYY )', 'appthemes-coupons' );

		return array(

			array(
				'title' => __( 'Coupon Code', 'appthemes-coupons' ),
				'type' => 'text',
				'name' => 'code',
				'extra' => array(
					'style' => 'width: 150px;'
				)
			),
			array(
				'title' => __( 'Discount Amount', 'appthemes-coupons' ),
				'type' => 'text',
				'name' => 'amount',
				'extra' => array(
					'style' => 'width: 50px;'
				)
			),
			array(
				'title' => __( 'Discount Type', 'appthemes-coupons' ),
				'type' => 'select',
				'name' => 'type',
				'values' => array(
					'flat' => sprintf( __( 'Flat Discount (%s)', 'appthemes-coupons' ), APP_Currencies::get_current_symbol() ),
					'percent' => __( 'Percentage Discount (%)', 'appthemes-coupons' )
				),
			),
			array(
				'title' => __( 'Start Date', 'appthemes-coupons' ),
				'type' => 'text',
				'name' => 'start_date',
				'desc' => $date_format,
				'extra' => array(
					'style' => 'width: 150px;'
				)
			),
			array(
				'title' => __( 'End Date', 'appthemes-coupons' ),
				'type' => 'text',
				'name' => 'end_date',
				'desc' => $date_format,
				'extra' => array(
					'style' => 'width: 150px;'
				)
			),
			array(
				'title' => __( 'Use Limit', 'appthemes-coupons' ),
				'type' => 'text',
				'name' => 'use_limit',
				'desc' => __( '( 0 for Unlimited )', 'appthemes-coupons' ),
				'extra' => array(
					'style' => 'width: 50px;'
				)
			),
			array(
				'title' => __( 'Use Limit Per User', 'appthemes-coupons' ),
				'type' => 'text',
				'name' => 'user_use_limit',
				'desc' => __( '( 0 for Unlimited )', 'appthemes-coupons' ),
				'extra' => array(
					'style' => 'width: 50px;'
				)
			),
		);

	}

}