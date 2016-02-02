<form id="bank-transfer">
	<fieldset>
		<div class="content">
			<p><?php echo ( isset( $options['message'] ) ) ? $options['message'] : '' ; ?></p>
		</div>
	</fieldset>
	<fieldset>
		<div class="featured-head">
			<h2 class="single dotted"><?php _e( 'Order Information:', APP_TD ); ?></h2>
		</div>
		<div class="content">
			<p><strong><?php _e( 'Order ID:', APP_TD ); ?></strong> <?php echo $order->get_id(); ?></p>
			<p><strong><?php _e( 'Order Total:', APP_TD ); ?></strong> <?php echo appthemes_get_price( $order->get_total(), $order->get_currency() ); ?></p>
			<p><?php _e( 'For questions or problems, please contact us directly at', APP_TD ) ?> <?php echo get_option('admin_email'); ?></p>
			<p><?php printf( __( 'To cancel this request and use a regular gateway instead, <a href="%s">click here</a>.', APP_TD ), get_the_order_cancel_url() ); ?></p>
		</div>
	</fieldset>
	<fieldset>
		<input type="submit" class="button" value="<?php _e( 'Continue &rsaquo;&rsaquo;', APP_TD ); ?>"  onClick="location.href='<?php echo esc_attr( add_query_arg( array( 'bt_end' => 1 ), appthemes_get_step_url( 'order-summary' ) ) ); ?>';return false;">
	</fieldset>
</form>
<div class="clr"></div>
