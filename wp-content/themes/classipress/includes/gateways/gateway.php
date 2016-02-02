<?php
/*
 * Payment gateway processing happens here
 *
 */



//membership system does not use $advals for security purposes
if ( !isset( $advals ) )
	$advals = $_POST;


if ( 'banktransfer' != $advals['cp_payment_method'] ) { ?>

	<center>
		<h2><?php _e( 'Please wait while we redirect you to our payment page.', APP_TD ); ?></h2>
		<div class="payment-loader"></div>
		<p class="small"><?php _e( '(Click the button below if you are not automatically redirected within 5 seconds.)', APP_TD ); ?></p>
	</center>

<?php
}


	// determine which payment gateway was selected and serve up the correct script
	if ( !isset( $advals['cp_payment_method'] ) )
		$advals['cp_payment_method'] = $_POST['cp_payment_method'];


	// membership purchase returns array of order values
	if ( !isset( $post_id ) ) {

		$order_vals = cp_get_order_pack_vals( $advals );

	// ad listing purchase returns array of order values
	} else {

		$advals['post_id'] = $post_id;
		$order_vals = cp_get_order_vals( $advals );

	}

	// do action hook
	cp_action_gateway( $order_vals );

?>


<div class="pad100"></div>
