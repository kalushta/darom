<?php
/**
 * Function to prevent visitors without admin permissions
 * to access the wordpress backend. If you wish to permit
 * others besides admins acces, change the user_level
 * to a different number.
 *
 * http://codex.wordpress.org/Roles_and_Capabilities#level_8
 *
 * @global <type> $user_level
 *
 */

function cp_security_check() {
	global $app_abbr;

	$cp_access_level = get_option($app_abbr.'_admin_security');
	// if there's no value then give everyone access
	if ( empty($cp_access_level) )
		$cp_access_level = 'read';

	// previous releases had incompatible capability with MU installs
	if ( 'install_themes' == $cp_access_level )
		$cp_access_level = 'manage_options';

	if ( !current_user_can($cp_access_level) ) {

?>

		<!DOCTYPE html>
		<html>

			<head>
				<title><?php _e( 'Access Denied.', APP_TD ); ?></title>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<link rel="stylesheet" href="<?php echo admin_url('css/install.css'); ?>" type="text/css" />
			</head>

			<body id="error-page">
				<p><?php _e( 'Access Denied. Your site administrator has blocked your access to the WordPress back-office.', APP_TD ); ?></p>
			</body>

		</html>

<?php
		exit();

	}

}

// if people are having trouble with this option, they can disable it
if ( get_option($app_abbr.'_admin_security') != 'disable' ) {

	// check and make sure security option is enabled and the request is not ajax which is used for search auto-complete
	if ( !defined('DOING_AJAX') )
		add_action('admin_init', 'cp_security_check', 1);

}

?>