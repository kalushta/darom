<?php

// BEFORE USING: Move the classiPress-child theme into the /themes/ folder.
//
// You can add you own actions, filters and code below.
//
// Remove below actions and "index.php" file from your child theme if you don't wish to have that homepage.

/**
 * Setup featured listings and pagination on homepage
 */  
function child_query_featured_ads_on_homepage( $query ) {
	if( $query->is_main_query() && $query->is_home() ) {
		$sticky = get_option('sticky_posts');
		$query->set( 'post_type', APP_POST_TYPE );
		$query->set( 'ignore_sticky_posts', 0 );
		$query->set( 'post__in', $sticky );
	}
}
add_action('pre_get_posts', 'child_query_featured_ads_on_homepage');


/**
 * Remove AppThemes actions
 */  
function child_remove_appthemes_actions() {
	// don't ignore sticky posts on homepage
	remove_action('pre_get_posts', 'cp_ignore_sticky_on_homepage');
}
add_action('appthemes_init', 'child_remove_appthemes_actions');
