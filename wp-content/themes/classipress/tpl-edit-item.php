<?php
/*
 * Template Name: User Edit Item
 *
 * This template must be assigned to the edit-item page
 * in order for it to work correctly
 *
*/

global $wpdb;

$debugOn = array();
$current_user = wp_get_current_user(); // grabs the user info and puts into vars

// get the ad id from the querystring.
$aid = appthemes_numbers_only($_GET['aid']);

// make sure the ad id is legit otherwise set it to zero which will return no results
if (!empty($aid)) $aid = $aid; else $aid = '0';

// select post information and also category with joins.
// filtering based off current user id which prevents people from trying to hack other peoples ads
$sql = $wpdb->prepare("SELECT wposts.*, $wpdb->term_taxonomy.term_id "
			. "FROM $wpdb->posts wposts "
			. "LEFT JOIN $wpdb->term_relationships ON($aid = $wpdb->term_relationships.object_id) "
			. "LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) "
			. "LEFT JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id) "
			. "WHERE ID = %s AND $wpdb->term_taxonomy.taxonomy = '".APP_TAX_CAT."' "
			//. "AND post_status <> 'draft' "// turned off to allow "paused" ads to be editable, uncomment to disable editing of paused ads
			. "AND post_author = %s", $aid, $current_user->ID);

// pull ad fields from db
$getad = $wpdb->get_row($sql);

?>


<script type='text/javascript'>
// <![CDATA[
jQuery(document).ready(function(){

	/* setup the form validation */
	jQuery("#mainform").validate({
		errorClass: 'invalid',
		errorPlacement: function(error, element) {
			if (element.attr('type') == 'checkbox' || element.attr('type') == 'radio') {
				element.closest('ol').before(error);
			} else {
				error.insertBefore(element);
			}
			error.css('display', 'block');
		}
	});

	/* setup the tooltip */
    jQuery("#mainform a").easyTooltip();

});


/* General Trim Function Based on Fastest Executable Trim */
function trim (str) {
    var	str = str.replace(/^\s\s*/, ''),
            ws = /\s/,
            i = str.length;
    while (ws.test(str.charAt(--i)));
    return str.slice(0, i + 1);
}

/* Used for enabling the image for uploads */
function enableNextImage($a, $i) {
	jQuery('#upload'+$i).removeAttr("disabled");
}

// ]]>
</script>

<?php
// call tinymce init code if html is enabled
if ( get_option('cp_allow_html') == 'yes' )
    appthemes_tinymce( 490, 300 );
?>

<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<!-- left block -->
			<div class="content_left">

				<div class="shadowblock_out">

					<div class="shadowblock">

						<h1 class="single dotted"><?php _e( 'Edit Your Ad', APP_TD ); ?></h1>

						<?php if ($getad && (get_option('cp_ad_edit') == 'yes') ): ?>

							<?php do_action( 'appthemes_notices' ); ?>

							<p><?php _e( 'Edit the fields below and click save to update your ad. Your changes will be updated instantly on the site.', APP_TD ); ?></p>

							<form name="mainform" id="mainform" class="form_edit" action="" method="post" enctype="multipart/form-data">

							<?php wp_nonce_field( 'cp-edit-item' ); ?>

								<ol>

								<?php
									// we first need to see if this ad is using a custom form
									// so lets search for a catid match and return the id if found
									$fid = cp_get_form_id($getad->term_id);

									// if there's no form id it must mean the default form is being used so let's go grab those fields
									if(!($fid)) {

										// use this if there's no custom form being used and give us the default form
										$sql = "SELECT field_label, field_name, field_type, field_values, field_tooltip, field_req FROM $wpdb->cp_ad_fields WHERE field_core = '1' ORDER BY field_id asc";

									} else {

										// now we should have the formid so show the form layout based on the category selected
										$sql = $wpdb->prepare("SELECT f.field_label, f.field_name, f.field_type, f.field_values, f.field_perm, f.field_tooltip, m.meta_id, m.field_pos, m.field_req, m.form_id "
													. "FROM $wpdb->cp_ad_fields f "
													. "INNER JOIN $wpdb->cp_ad_meta m "
													. "ON f.field_id = m.field_id "
													. "WHERE m.form_id = %s "
													. "ORDER BY m.field_pos asc", $fid);

									}

									$results = $wpdb->get_results($sql);

									if ($results) {
										// build the edit ad form
										cp_formbuilder($results, $getad);
									}

									// check and make sure images are allowed
									if ( get_option('cp_ad_images') == 'yes' ) {

										if ( appthemes_plupload_is_enabled() ) {
											echo appthemes_plupload_form($getad->ID);
										} else {
											$imagecount = cp_get_ad_images($getad->ID);
											// print out image upload fields. pass in count of images allowed
											echo cp_ad_edit_image_input_fields($imagecount);
										}

									} else { ?>

										<div class="pad10"></div>
										<li>
											<div class="labelwrapper">
												<label><?php _e( 'Images:', APP_TD ); ?></label><?php _e( 'Sorry, image editing is not supported for this ad.', APP_TD ); ?>
											</div>
										</li>
										<div class="pad25"></div>

									<?php } ?>


									<p class="submit center">
										<input type="button" class="btn_orange" onclick="window.location.href='<?php echo CP_DASHBOARD_URL; ?>'" value="<?php _e( 'Cancel', APP_TD ); ?>" />&nbsp;&nbsp;
										<input type="submit" class="btn_orange" value="<?php _e( 'Update Ad &raquo;', APP_TD ); ?>" name="submit" />
									</p>

								</ol>

								<input type="hidden" name="action" value="cp-edit-item" />
								<input type="hidden" name="ad_id" value="<?php echo $getad->ID; ?>" />

							</form>

						<?php else : ?>

							<p class="text-center"><?php _e( 'You have entered an invalid ad id or do not have permission to edit that ad.', APP_TD ); ?></p>

						<?php endif; ?>

					</div><!-- /shadowblock -->

				</div><!-- /shadowblock_out -->

			</div><!-- /content_left -->

			<?php get_sidebar( 'user' ); ?>

			<div class="clr"></div>

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
