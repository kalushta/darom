<?php
/**
 * This is step 3 of 3 for the ad submission form
 * 
 * @package ClassiPress
 * @subpackage Membership
 * @author AppThemes
 *
 *
 */

global $wpdb, $order, $current_user, $cp_user_orders, $cp_user_recent_order;

//check to make sure the user has an order already setup, othrewise the page was refreshed or page hack was attempted
if ( count($cp_user_orders) > 0 ) {
?>

	<div id="step3"></div>

	<h2 class="dotted">
		<?php if ( $_POST['total_cost'] > 0 ) { _e( 'Final Step', APP_TD ); } else { _e( 'Membership Updated', APP_TD ); } ?>
	</h2>

	<img src="<?php bloginfo('template_url'); ?>/images/step3.gif" alt="" class="stepimg" />

	<?php
		// call in the selected payment gateway as long as the price isn't zero
		if ( $order['total_cost'] > 0 ) {
			include_once (TEMPLATEPATH . '/includes/gateways/gateway.php');

		//process the "free" orders on this page, the payment gateway orders will be processed on tpl-membership-purchase.php
		} else { 
			$order_processed = appthemes_process_membership_order($current_user, $order);
			//send email to user
			if ( $order_processed )
				cp_owner_activated_membership_email($current_user, $order_processed);
	?>

			<div class="thankyou">

				<h3><?php _e( 'Your order has been completed and your membership status should now be active.', APP_TD ); ?></h3>

				<p><?php _e( 'Visit your dashboard to review your membership status details.', APP_TD ); ?></p>

				<ul class="membership-pack">
					<li><strong><?php _e( 'Membership Pack:', APP_TD ); ?></strong> <?php echo stripslashes($order_processed['pack_name']); ?></li>
					<li><strong><?php _e( 'Membership Expires:', APP_TD ); ?></strong> <?php echo appthemes_display_date($order_processed['updated_expires_date']); ?></li>
				</ul>

				<div class="pad50"></div>

			</div> <!-- /thankyou -->

			<?php do_action('appthemes_after_membership_confirmation'); ?>

		<?php
			// remove the order option from the database because the free order was processed
			delete_option($cp_user_recent_order);
	
		}

		// send new membership notification email to admin
		//if (get_option('cp_new_membership_email') == 'yes' || $_POST['cp_payment_method'] == 'banktransfer')
		//    cp_new_membership_email($order['order_id']);


} else {
?>

	<h2 class="dotted"><?php _e( 'An Error Has Occurred', APP_TD ); ?></h2>

	<div class="thankyou">
		<p><?php _e( 'Your session or order has expired or we cannot cannot find your order in our systems. Please start over to create a valid membership order.', APP_TD ); ?></p>
	</div>

<?php
}
?>

	<div class="pad100"></div>
