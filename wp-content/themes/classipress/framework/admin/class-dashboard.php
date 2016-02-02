<?php

class APP_Dashboard extends scbBoxesPage {

	const NEWS_FEED = 'http://feeds2.feedburner.com/appthemes';
	const TWITTER_FEED = 'http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=appthemes';

	function __construct( $args ) {
		$this->args = wp_parse_args( $args, array(
			'page_slug' => 'app-dashboard',
			'toplevel' => 'menu',
			'position' => 3,
			'screen_icon' => 'themes',
		) );

		$this->boxes = array(
			array( 'news', $this->box_icon( 'newspaper.png' ) . __( 'Latest News', APP_TD ), 'normal' ),
			array( 'twitter', $this->box_icon( 'twitter-bird.png' ) . __( 'Latest Tweets', APP_TD ), 'side' ),
		);

		scbAdminPage::__construct();
	}

	function news_box() {
		echo '<div class="rss-widget">';
		wp_widget_rss_output( self::NEWS_FEED, array( 'items' => 5, 'show_author' => 0, 'show_date' => 1, 'show_summary' => 1 ) );
		echo '</div>';
	}

	function twitter_box() {
		echo '<div class="rss-widget">';
		wp_widget_rss_output( self::TWITTER_FEED, array( 'items' => 5, 'show_author' => 0, 'show_date' => 1, 'show_summary' => 1 ) );
		echo '</div>';
	}

	function page_init() {
		// This will be enqueued on all admin pages
		wp_enqueue_style( 'app-admin', get_template_directory_uri() . '/includes/admin/admin.css' );

		parent::page_init();

		extract( $this->args );

		// Make the first submenu read 'Dashboard', not the top-level title
		$this->pagehook = add_submenu_page( $page_slug, $page_title, __( 'Dashboard', APP_TD ), $capability, $page_slug, array( $this, '_page_content_hook' ) );
	}

	protected function box_icon( $name ) {
		return html( 'img', array(
			'class' => 'box-icon',
			'src' => appthemes_framework_image( $name )
		) );
	}

	function page_head() {
		wp_enqueue_style( 'dashboard' );

?>
<style type="text/css">
.postbox {
	position: relative;
}

.postbox .hndle span {
	padding-left: 21px;
}

.box-icon {
	position: absolute;
	top: 7px;
	left: 10px;
}
</style>
<?php
	}
}

