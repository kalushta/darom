<?php
/**
 *
 * Holding Depricated functions oldest at the bottom (delete and clean as needed)
 * @package ClassiPress
 * @author AppThemes
 *
 */


/**
 * Categories list.
 *
 * @deprecated 3.1.9
 * @deprecated Use cp_create_categories_list()
 * @see cp_create_categories_list()
 */
if ( !function_exists('cp_cat_menu_drop_down') ) {
	function cp_cat_menu_drop_down( $cols = 3, $subs = 0 ) {
		_deprecated_function( __FUNCTION__, '3.1.9', 'cp_create_categories_list()' );

		return cp_create_categories_list( 'dir' );
	}
}


/**
 * Directory home page category display.
 *
 * @deprecated 3.0.5.2
 * @deprecated Use cp_create_categories_list()
 * @see cp_create_categories_list()
 */
if ( !function_exists('cp_directory_cat_columns') ) {
	function cp_directory_cat_columns($cols) {
		_deprecated_function( __FUNCTION__, '3.0.5.2', 'cp_create_categories_list()' );

		return cp_create_categories_list( 'dir' );
	}
}


/**
 * Create geocodes database table.
 *
 * @deprecated 3.2
 * @deprecated Use 'appthemes_first_run' hook
 * @see appthemes_first_run' hook
 */
if ( !function_exists('cp_create_geocode_table') ) {
	function cp_create_geocode_table() {
		_deprecated_function( __FUNCTION__, '3.2', 'appthemes_first_run' );

		return false;
	}
}


/**
 * Get the ad price and position the currency symbol.
 * Meta field 'price' used on CP 2.9.3 and earlier
 *
 * @deprecated 3.2
 * @deprecated Use cp_get_price()
 * @see cp_get_price()
 */
function cp_get_price_legacy($postid) {
	_deprecated_function( __FUNCTION__, '3.2', 'cp_get_price' );

	return cp_get_price($postid, 'price');
}


/**
 * Builds the edit ad form on the tpl-edit-item.php page template.
 *
 * @deprecated 3.2.1
 * @deprecated Use cp_formbuilder()
 * @see cp_formbuilder()
 */
if ( ! function_exists('cp_edit_ad_formbuilder') ) {
	function cp_edit_ad_formbuilder( $results, $post ) {
		_deprecated_function( __FUNCTION__, '3.2.1', 'cp_formbuilder' );

		include_once( TEMPLATEPATH . '/includes/forms/step-functions.php' );

		return cp_formbuilder( $results, $post );
	}
}


/*
 * DEPRICATED 
 * @VERSION 3.0.5.4
 * @LOCATION theme-emails.php
 * TURNED OFF AS IT WAS OVERWRITING OUR CUSTOM EMAIL FROM ADDRESSES, IF ITS STILL NEEDED, THEN IT NEEDS ANOTHER IF STATEMENT FOR USE.

// overwrite the default generic WordPress from name and email address
if(get_option('cp_custom_email_header') == 'yes') {
	
    if (!class_exists('wp_mail_from')) :
        class wp_mail_from {

            function wp_mail_from() {
                add_filter('wp_mail_from', array(&$this, 'cp_mail_from'));
                add_filter('wp_mail_from_name', array(&$this, 'cp_mail_from_name'));
            }

            // new from name
            function cp_mail_from_name() {
				if(get_option('cp_nu_custom_email') == 'yes') $name = get_option('cp_nu_from_name');
                else $name = get_option('blogname');
                $name = esc_attr($name);
                return $name;
            }

            // new email address
            function cp_mail_from() {
				if(get_option('cp_nu_custom_email') == 'yes') $email = get_option('cp_nu_from_email');
                else $email = get_option('admin_email');
                $email = is_email($email);
                return $email;
            }

        }

        $wp_mail_from = new wp_mail_from();

    endif;

}
*/




?>