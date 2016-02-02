<?php
/**
 *
 * Cron jobs that are executed on a timer ClassiPress
 * @package ClassiPress
 * @author AppThemes
 * @version 3.0.4
 * 
 */


// schedule the expired ads check
function cp_schedule_expire_check() {
	$recurrance = get_option('cp_ad_expired_check_recurrance');
	if ( empty( $recurrance ) )
		$recurrance = 'daily';

	// clear schedule if prune ads disabled or recurrance set to none
	if ( get_option('cp_post_prune') != 'yes' || $recurrance == 'none' ) {
		if ( wp_next_scheduled('cp_ad_expired_check') )
			wp_clear_scheduled_hook('cp_ad_expired_check');
		return;
	}

	// set schedule if does not exist
	if ( ! wp_next_scheduled('cp_ad_expired_check') ) {
		wp_schedule_event(time(), $recurrance, 'cp_ad_expired_check');
		return;
	}

	// re-schedule if settings changed
	$schedule = wp_get_schedule('cp_ad_expired_check');
	if ( $schedule && $schedule != $recurrance ) {
		wp_clear_scheduled_hook('cp_ad_expired_check');
		wp_schedule_event(time(), $recurrance, 'cp_ad_expired_check');
	}

}
add_action( 'init', 'cp_schedule_expire_check' );


//cron jobs execute the following function everytime they run
function cp_check_expired_cron() {
    if( get_option('cp_post_prune') == 'yes' ) {
        global $wpdb;

        //keep in mind the email takes the tabbed formatting and uses it in the email, so please keep formatting of query below.
        $qryToString = "SELECT $wpdb->posts.ID FROM $wpdb->posts
        LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
        WHERE $wpdb->postmeta.meta_key = 'cp_sys_expire_date'
        AND timediff(STR_TO_DATE($wpdb->postmeta.meta_value, '%m/%d/%Y %H:%i:%s'), now()) <= 0
        AND $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = '".APP_POST_TYPE."'";

				//alternative sql query for hosts where TIMEDIFF working incorrectly, to enable define 'CP_ALTERNATIVE_PRUNE_SQL' constant
				if ( defined('CP_ALTERNATIVE_PRUNE_SQL') && CP_ALTERNATIVE_PRUNE_SQL ) {
					$qryToString = "SELECT $wpdb->posts.ID FROM $wpdb->posts
							LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
							WHERE $wpdb->postmeta.meta_key = 'cp_sys_expire_date'
							AND TIMESTAMPDIFF(HOUR, now(), STR_TO_DATE($wpdb->postmeta.meta_value, '%m/%d/%Y %H:%i:%s')) <= 0
							AND $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = '".APP_POST_TYPE."'";
				}

        $postids = $wpdb->get_col($qryToString);
        $messageDetails = '';

        //create msgDetails variable that has a set of links to expired ads that are found.
        if ($postids) foreach ($postids as $id) {
            $update_ad = array();
            $update_ad['ID'] = $id;
            $update_ad['post_status'] = 'draft';
            wp_update_post($update_ad);
            $messageDetails .= get_bloginfo ( 'url' ) . "/?p=" . $id . '' . "\r\n";
        }
        //get rid of the trailing comma
        $messageDetails = trim($messageDetails, ',');

        if($messageDetails == '')
            $messageDetails = __( 'No expired ads were found.', APP_TD );
        else
            $messageDetails = __( 'The following ads expired and have been taken down from your website: ', APP_TD ) . "\r\n" . $messageDetails;
        $message = __( 'Your cron job has run successfully. ', APP_TD ) . "\r\n" . $messageDetails;
    } else {
        $message = __( 'Your cron job has run successfully. However, the pruning ads option is turned off so no expired ads were taken down from the website.', APP_TD ); }

        if(get_option('cp_prune_ads_email') == 'yes')
            wp_mail(get_option('admin_email'), __( 'ClassiPress Ads Expired', APP_TD ), $message . "\r\n\n" . __( 'Regards', APP_TD ) . ", \r\n" . __( 'ClassiPress', APP_TD ));
}

add_action('cp_ad_expired_check', 'cp_check_expired_cron');


// sends email reminder about ending membership plan, default is 7 days before expire
// cron jobs execute the following function once per day
function cp_membership_reminder_cron() {
    if( get_option('cp_membership_ending_reminder_email') == 'yes' ) {
        global $wpdb;

        $days_before = get_option('cp_membership_ending_reminder_days');
        $days_before = is_numeric($days_before) ? $days_before : 7;
        $timestamp = wp_next_scheduled('cp_send_membership_reminder');
        $timestamp -= (1 * 24 * 60 * 60); // minus 1 day to get current schedule time on which in theory function should to execute
      	$date_max = date('Y-m-d H:i:s', $timestamp + (($days_before) * 24 * 60 * 60));
      	$date_min = date('Y-m-d H:i:s', $timestamp + (($days_before - 1) * 24 * 60 * 60));

        $qryToString = $wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users
        LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id
        WHERE $wpdb->usermeta.meta_key = 'membership_expires'
        AND $wpdb->usermeta.meta_value < %s
        AND $wpdb->usermeta.meta_value > %s
        ", $date_max, $date_min);

        $userids = $wpdb->get_col($qryToString);

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $siteurl = trailingslashit(get_option('home'));

        if ($userids) foreach ($userids as $user_id) {
            $the_user = get_userdata($user_id);
            $mailto = $the_user->user_email;
            $user_login = stripslashes($the_user->user_login);

            $membership = get_pack($the_user->active_membership_pack);
            $membership_pack_name = stripslashes($membership->pack_name);

            $subject = sprintf( __( 'Membership Subscription Ending on %s', APP_TD ), $blogname );
            $headers = 'From: '. sprintf( __('%s Admin', APP_TD ), $blogname ) .' <'. get_option('admin_email') .'>' . "\r\n";

            $message  = sprintf( __( 'Hi %s,', APP_TD ), $user_login ) . "\r\n\r\n";
            $message .= sprintf( __( 'Your membership pack will expire in %d days! Please renew your membership to continue posting classified ads.', APP_TD ), $days_before ) . "\r\n\r\n";

            $message .= __( 'Membership Details', APP_TD ) . "\r\n";
            $message .= __( '-----------------', APP_TD ) . "\r\n";
            $message .= __( 'Membership Pack: ', APP_TD ) . $membership_pack_name . "\r\n";
            $message .= __( 'Membership Expires: ', APP_TD ) . $the_user->membership_expires . "\r\n";
            $message .= __( 'Renew Your Membership Pack: ', APP_TD ) . CP_MEMBERSHIP_PURCHASE_URL . "\r\n\r\n";

            $message .= __( 'For questions or problems, please contact us directly at', APP_TD ) . " " . get_option('admin_email') . "\r\n\r\n\r\n\r\n";
            $message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
            $message .= sprintf( __( 'Your %s Team', APP_TD ), $blogname ) . "\r\n";
            $message .= $siteurl . "\r\n\r\n\r\n\r\n";

            wp_mail($mailto, $subject, $message, $headers);
        }

    }
}
add_action('cp_send_membership_reminder', 'cp_membership_reminder_cron');


// Schedules a daily event to send membership reminder emails
function cp_schedule_membership_reminder() {
	if (!wp_next_scheduled('cp_send_membership_reminder'))
		wp_schedule_event(time(), 'daily', 'cp_send_membership_reminder');
}
add_action('init', 'cp_schedule_membership_reminder');


?>