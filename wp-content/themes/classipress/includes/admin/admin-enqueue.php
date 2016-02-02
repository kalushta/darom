<?php
/**
 * These are scripts used within the AppThemes admin pages
 *
 * @package AppThemes
 *
 */


// correctly load all the scripts so they don't conflict with plugins
function appthemes_load_admin_scripts() {
	global $pagenow;

	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('media-upload'); // needed for image upload
	wp_enqueue_script('thickbox'); // needed for image upload
	wp_enqueue_style('thickbox'); // needed for image upload


	//TODO: For now we call these on all admin pages because of some javascript errors, however it should be registered per admin page (like wordpress does it)
	wp_enqueue_script('jquery-ui-sortable'); //this script has issues on the page edit.php?post_type=ad_listing


	//timepicker requires datepicker in order to work
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('timepicker', get_bloginfo('template_directory').'/includes/js/timepicker.min.js', array('jquery-ui-core', 'jquery-ui-datepicker'), '1.0.0');

	wp_register_style( 'jquery-ui-style', get_bloginfo( 'template_directory' ) . '/includes/js/jquery-ui/jquery-ui.css', false, '1.9.2' );
	wp_enqueue_style( 'jquery-ui-style' );

	wp_enqueue_script('easytooltip', get_bloginfo('template_directory').'/includes/js/easyTooltip.js', array('jquery'), '1.0');

	if ($pagenow == 'admin.php') // only trigger this on CP edit pages otherwise it causes a conflict with edit ad and edit post meta field buttons
		wp_enqueue_script('validate', get_bloginfo('template_directory').'/includes/js/validate/jquery.validate.min.js', array('jquery-ui-core'), '1.6');

	wp_enqueue_script('admin-scripts', get_bloginfo('template_directory').'/includes/admin/admin-scripts.js', array('jquery','media-upload','thickbox'), '1.2');

	wp_enqueue_script('excanvas', get_bloginfo('template_directory').'/includes/js/excanvas.min.js', array('jquery'), '1.0');
	wp_enqueue_script('flot', get_bloginfo('template_directory').'/includes/js/jquery.flot.min.js', array('excanvas'), '0.6');

	wp_register_style('admin-style', get_bloginfo('template_directory').'/includes/admin/admin-style.css', false, '3.0');
	wp_enqueue_style('admin-style');


}


add_action('admin_enqueue_scripts', 'appthemes_load_admin_scripts');


?>