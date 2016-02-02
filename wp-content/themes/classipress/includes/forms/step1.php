<?php
/**
 * This is step 1 of 3 for the ad submission form
 *
 * @package ClassiPress
 * @subpackage New Ad
 * @author AppThemes
 *
 *
 */

global $current_user, $wpdb;
$change_cat_url = add_query_arg( array( 'action' => 'change' ) );
?>

<div id="step1">

	<h2 class="dotted"><?php _e( 'Submit Your Listing', APP_TD ); ?></h2>

	<img src="<?php bloginfo('template_url'); ?>/images/step1.gif" alt="" class="stepimg" />

	<?php
		do_action( 'appthemes_notices' );
		// display the custom message
		echo get_option('cp_ads_form_msg');
	?>

	<p class="dotted">&nbsp;</p>

	<?php
		// show the category dropdown when first arriving to this page.
		// Also show it if cat equals -1 which means the 'select one' option was submitted
		if ( !isset($_POST['cat']) || ($_POST['cat'] == '-1') ) {
	?>

<style type="text/css"> /* hack on new ad submission page until we fix multi-level dropdown issue with .js */
	div#catlvl0 select#cat.dropdownlist, div#childCategory select#cat.dropdownlist, form#mainform.form_step select {padding:6px;height: 3em; color:#666; font-size:12px; display: block;}
</style>

<script type="text/javascript">
	// <![CDATA[
		jQuery(document).ready(function($) {
			/* remove the select dropdown menus style - hack until we can fix the multi-level dropdown js issue */
			$('select').selectBox('destroy');
		});
	// ]]>
</script>

	<form name="mainform" id="mainform" class="form_step" action="" method="post">

		<ol>

			<li>
				<div class="labelwrapper"><label><?php _e( 'Cost Per Listing:', APP_TD ); ?></label></div>
				<?php cp_cost_per_listing(); ?>
				<div class="clr"></div>
			</li>

			<?php
				$category = ( isset($_GET['renew']) ) ? true : false;
				if ( $category ) {
					$terms = wp_get_post_terms( $_GET['renew'], APP_TAX_CAT );
					$category = ( !empty($terms) ) ? $terms[0] : false;
				}

				if ( !isset($_GET['renew']) || !$category || ( isset($_GET['action']) && $_GET['action'] == 'change' ) ) {
			?>

			<li>
				<div class="labelwrapper"><label><?php _e( 'Select a Category:', APP_TD ); ?></label></div>
				<div id="ad-categories" style="display:block; margin-left:170px;">						
					<div id="catlvl0">
						<?php

							if ( get_option('cp_price_scheme') == 'category' && get_option('cp_charge_ads') == 'yes' && get_option('cp_ad_parent_posting') != 'no' ) {
								cp_dropdown_categories_prices('show_option_none=' . __( 'Select one', APP_TD ) . '&class=dropdownlist&orderby=name&order=ASC&hide_empty=0&hierarchical=1&taxonomy='.APP_TAX_CAT.'&depth=1');
							} else {
 								wp_dropdown_categories('show_option_none=' . __( 'Select one', APP_TD ) . '&class=dropdownlist&orderby=name&order=ASC&hide_empty=0&hierarchical=1&taxonomy='.APP_TAX_CAT.'&depth=1');
							}

						?>
						<div style="clear:both;"></div>
					</div>
				</div>
				<div id="ad-categories-footer" class="button-container">
					<input type="submit" name="getcat" id="getcat" class="btn_orange" value="<?php _e( 'Go &rsaquo;&rsaquo;', APP_TD ); ?>" />
					<div id="chosenCategory"><input id="cat" name="cat" type="input" value="-1" /></div>
					<div style="clear:both;"></div>
				</div>
				<div style="clear:both;"></div>
			</li>

			<?php
				} else {
					if ( get_option('cp_price_scheme') == 'category' && get_option('cp_charge_ads') == 'yes' ) {
						$cat_fee = get_option('cp_cat_price_'.$category->term_id);
						$cat_fee = ' - ' . cp_display_price($cat_fee, '', false);
					} else {
						$cat_fee = '';
					}
			?>

			<li>
				<div class="labelwrapper"><label><?php _e( 'Category:', APP_TD ); ?></label></div>
				<strong><?php echo $category->name; ?></strong><?php echo $cat_fee; ?>&nbsp;&nbsp;<small><a href="<?php echo $change_cat_url; ?>"><?php _e( '(change)', APP_TD ); ?></a></small>
				<div class="clr pad5"></div>
				<div class="button-container">
					<input id="cat" name="cat" type="hidden" value="<?php echo $category->term_id; ?>" />
					<input type="submit" name="getcat" class="btn_orange" value="<?php _e( 'Go &rsaquo;&rsaquo;', APP_TD ); ?>" />
				</div>
			</li>

			<?php } ?>

		</ol>

	</form>


	<?php } else {
		// show the form based on the category selected
		// get the cat nice name and put it into a variable
		$adCategory = get_term_by( 'id', $_POST['cat'], APP_TAX_CAT );
		$_POST['catname'] = $adCategory->name;
	?>

	<form name="mainform" id="mainform" class="form_step" action="" method="post" enctype="multipart/form-data">

		<ol>

			<li>
				<div class="labelwrapper"><label><?php _e( 'Category:', APP_TD ); ?></label></div>
				<strong><?php echo $_POST['catname']; ?></strong>&nbsp;&nbsp;<small><a href="<?php echo $change_cat_url; ?>"><?php _e( '(change)', APP_TD ); ?></a></small>
			</li>

			<?php
				$renew_id = ( isset($_GET['renew']) ) ? $_GET['renew'] : false;
				$renew = ( $renew_id ) ? get_post($renew_id) : false;
				echo cp_show_form($_POST['cat'], $renew);
			?>

			<p class="btn1">
				<input type="submit" name="step1" id="step1" class="btn_orange" value="<?php _e( 'Continue &rsaquo;&rsaquo;', APP_TD ); ?>" />
			</p>

		</ol>

		<input type="hidden" id="cat" name="cat" value="<?php echo $_POST['cat']; ?>" />
		<input type="hidden" id="catname" name="catname" value="<?php echo $_POST['catname']; ?>" />
		<input type="hidden" id="fid" name="fid" value="<?php if ( isset($_POST['fid']) ) echo $_POST['fid']; ?>" />
		<input type="hidden" id="oid" name="oid" value="<?php echo $order_id; ?>" />

	</form>

	<?php } ?>

</div>
