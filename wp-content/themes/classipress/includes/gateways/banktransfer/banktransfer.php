<?php
/**
 * Bank transfer payment gateway script
 *
 * @package ClassiPress
 * @author AppThemes
 *
 * @param array $order_vals contains all the order values
 *
 */



/**
 * payment processing script that is used on the new ad confirmation page
 *
 * @since 3.0.4
 */
function banktransfer_gateway_process( $order_vals ) {
	global $gateway_name, $app_abbr, $ref_val;

	// if gateway wasn't selected then exit
	if ( $order_vals['cp_payment_method'] != 'banktransfer' )
		return;

	$ad_listing = !empty($order_vals['post_id']) ? true : false;
	// ad listing or membership
	if ( $ad_listing ) {
		$ref_val = $order_vals['post_id'];
		$info_message = __( 'Please include the following details when sending the bank transfer. Once your transfer has been verified, we will then approve your ad listing.', APP_TD );
		cp_bank_owner_new_ad_email($ref_val);
	} else {
		$ref_val = $order_vals['oid'];
		$info_message = __( 'Please include the following details when sending the bank transfer. Once your transfer has been verified, we will then activate your membership.', APP_TD );
		cp_new_membership_email($ref_val);
		cp_bank_owner_new_membership_email($ref_val);
	}

	// regardless of what happens, log the transaction
	if ( file_exists(TEMPLATEPATH . '/includes/gateways/process.php') ) {
		include_once (TEMPLATEPATH . '/includes/gateways/process.php');

		$trdata = cp_prepare_transaction_entry($order_vals);
		if ( $trdata )
			$tr_id = cp_add_transaction_entry($trdata);
	}
?>

	<h2><?php _e( 'Your Unique Ad Details', APP_TD ); ?></h2>

	<p><?php echo $info_message; ?></p>

	<p>
		<strong><?php if ( $ad_listing ) _e( 'Transaction ID:', APP_TD ); else _e( 'Pack Name:', APP_TD ); ?></strong> <?php echo esc_html( $order_vals['item_number'] ); ?><br />
		<strong><?php if ( $ad_listing ) _e( 'Reference #:', APP_TD ); else _e( 'Transaction ID:', APP_TD ); ?></strong> <?php echo esc_attr( $ref_val ); ?><br />
		<strong><?php _e( 'Total Amount:', APP_TD ); ?></strong> <?php cp_display_price($order_vals['item_amount'], get_option('cp_curr_pay_type')); ?><br />
	</p>

	<br /><br />

	<h2><?php _e( 'Bank Transfer Instructions', APP_TD ); ?></h2>

	<p><?php echo stripslashes( appthemes_nl2br( get_option('cp_bank_instructions') ) ); ?></p>

	<p><?php _e( 'For questions or problems, please contact us directly at', APP_TD ); ?> <?php echo get_option('admin_email'); ?></p>

<?php
}
add_action( 'cp_action_gateway', 'banktransfer_gateway_process', 10, 1 );


/**
 * add the option to the payment drop-down list on checkout
 *
 * @since 3.2
 */
function cp_banktransfer_add_gateway_option() {
	global $app_abbr, $gateway_name;

	if ( get_option($app_abbr.'_enable_bank') == 'yes' )
		echo '<option value="banktransfer">' . __( 'Bank Transfer', APP_TD ) . '</option>';

}
add_action( 'cp_action_payment_method', 'cp_banktransfer_add_gateway_option' );


?>