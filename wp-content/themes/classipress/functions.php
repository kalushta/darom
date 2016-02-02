<?php
/**
 * Theme functions file
 *
 * DO NOT MODIFY THIS FILE. Make a child theme instead: http://codex.wordpress.org/Child_Themes
 *
 * @package ClassiPress
 * @author AppThemes
 */


// current version
$app_theme = 'ClassiPress';
$app_abbr = 'cp';
$app_version = '3.2.1';
$app_db_version = 1320;
$app_edition = 'Ultimate Edition';
$app_stats = 'today';

// define rss feed urls
$app_rss_feed = 'http://feeds2.feedburner.com/appthemes';
$app_twitter_rss_feed = 'http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=appthemes';
$app_forum_rss_feed = 'http://forums.appthemes.com/external.php?type=RSS2';

// define the transients we use
$app_transients = array($app_abbr.'_cat_menu');

define( 'APP_TD', 'classipress' );

// Framework
require( dirname(__FILE__) . '/framework/load.php' );

scb_register_table( 'app_pop_daily', $app_abbr . '_ad_pop_daily' );
scb_register_table( 'app_pop_total', $app_abbr . '_ad_pop_total' );

require( dirname(__FILE__) . '/framework/includes/stats.php' );

if ( is_admin() )
	require( dirname(__FILE__) . '/framework/admin/importer.php' );

// Theme-specific files
require( dirname(__FILE__) . '/includes/theme-functions.php' );

