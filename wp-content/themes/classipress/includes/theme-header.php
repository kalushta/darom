<?php

/**
 * Add header elements via the wp_head hook
 *
 * Anything you add to this file will be dynamically
 * inserted in the header of your theme
 *
 * @since 3.0.0
 * @uses wp_head
 *
 */


// adds CP version number in the header for troubleshooting
function cp_version($app_version) {
	global $app_version;
	echo '<meta name="version" content="ClassiPress '.$app_version.'" />' . "\n";
}
add_action('wp_head', 'cp_version');


// adds support for cufon font replacement
function cp_cufon_styles() { //TODO: move into theme-enqueue

	if ( get_option('cp_cufon_enable') != 'yes' ) return;
?>
	<!--[if gte IE 9]> <script type="text/javascript"> Cufon.set('engine', 'canvas'); </script> <![endif]-->
	<!-- cufon fonts  -->
		<script src="<?php bloginfo('template_directory'); ?>/includes/fonts/Vegur_400-Vegur_700.font.js" type="text/javascript"></script>
		<script src="<?php bloginfo('template_directory'); ?>/includes/fonts/Liberation_Serif_400.font.js" type="text/javascript"></script>
	<!-- end cufon fonts  -->

	<!-- cufon font replacements -->
	<script type="text/javascript">
		// <![CDATA[
		<?php echo stripslashes(get_option('cp_cufon_code')). "\n"; ?>
		// ]]>
	</script>
	<!-- end cufon font replacements -->

<?php
}
add_action('wp_head', 'cp_cufon_styles');


// remove the WordPress version meta tag
if (get_option('cp_remove_wp_generator') == 'yes')
	remove_action('wp_head', 'wp_generator');


// remove the new 3.1 admin header toolbar visible on the website if logged in
if (get_option('cp_remove_admin_bar') == 'yes')
	add_filter('show_admin_bar', '__return_false');

?>