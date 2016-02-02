<?php
/*
Template Name: Membership Pack Purchases
*/


global $current_user;
$current_user = wp_get_current_user();

//get the membership information based on selected membership pack ID
$active_membership = isset($current_user->active_membership_pack) ? get_pack($current_user->active_membership_pack) : false;
$coupon = isset($_POST['coupon']) ? $_POST['coupon'] : false;

if(isset($_POST['pack'])) {
	$membership = get_pack($_POST['pack']);
	$total_cost = cp_calc_membership_cost($_POST['pack'], $coupon);
}

//get any existing orders
$cp_user_orders = get_user_orders($current_user->ID);
if(isset($cp_user_orders) && $cp_user_orders)
	$cp_user_recent_order = $cp_user_orders[0];

?>

<script type='text/javascript'>
// <![CDATA[
/* setup the tooltip */
jQuery(document).ready(function(){jQuery("#mainform a").easyTooltip();});
// ]]>
</script>

<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<!-- full block -->
			<div class="shadowblock_out">

				<div class="shadowblock">

				<?php if(get_option('cp_enable_membership_packs') == 'yes') : ?>

					<?php
						// check and make sure the form was submitted from step1 and the session value exists
						if(isset($_POST['step1'])) {

							include_once(TEMPLATEPATH . '/includes/forms/step2-membership.php');

						} elseif(isset($_POST['step2'])) {
							//now put the array containing all the post values into the database
							//DO NOTE USE POST VARS execpt as the relate to options selected. All POST vars are insecure.
							$membership = get_pack($_POST['pack']);
							$order = array();
							$order['user_id'] = $current_user->ID;
							$order['order_id'] = $_POST['oid'];
							$order['option_order_id'] = 'cp_order_'.$current_user->ID.'_'.$_POST['oid'];
							$order['pack_type'] = 'membership';
							$order['total_cost'] = $total_cost;

							//Check for coupon and use to to reduce total price and total number of coupon uses left
							if(isset($_POST['cp_coupon_code'])) {
								$order['cp_coupon_code'] = $_POST['cp_coupon_code'];
								//increment coupon code count only if total ad price was not zero
								if(cp_check_coupon_discount($order['cp_coupon_code'])) {
									cp_use_coupon($order['cp_coupon_code']);
									//TODO - handle discounting of total cost
								}
							}

							//save the order for use when payment is completed
							$order = array_merge($order, (array)$membership);
							update_option($order['option_order_id'], $order);
							$cp_user_orders = get_user_orders($current_user->ID);
							if(isset($cp_user_orders) && $cp_user_orders) $cp_user_recent_order = $cp_user_orders[0];
							include_once(TEMPLATEPATH . '/includes/forms/step3-membership.php');

						} else {

							// create a unique ID for this new ad order
							// uniqid requires a param for php 4.3 or earlier. added for 3.0.1
							if(empty($cp_user_recent_order)) {
								$order_id  = uniqid(rand(10,1000), false);
							} else {
								$order_id = get_order_id($cp_user_recent_order);
							}
							include_once(TEMPLATEPATH . '/includes/forms/step1-membership.php');

						} ?>

				<?php else : ?>

					<h2 class="dotted"><?php _e( 'Membership Not Enabled', APP_TD ); ?></h2>

					<div class="info">
						<p><?php _e( 'Administrator currently has memberships disabled. Please try again later.', APP_TD ); ?></p>
					</div>

					<div class="pad100"></div>

				<?php endif; //endif cp_enable_membership_packs ?>

				</div><!-- /shadowblock -->

			</div><!-- /shadowblock_out -->

			<div class="clr"></div>

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
