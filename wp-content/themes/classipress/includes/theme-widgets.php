<?php
/**
 * Widgets available for the theme
 *
 */


// widget to show all ad categories
function cp_ad_cats_widget() {
?>
	<div class="shadowblock_out">

		<div class="shadowblock">

			<h2 class="dotted"><?php _e( 'Ad Categories', APP_TD ); ?></h2>

			<div class="recordfromblog">

				<ul>
					<?php wp_list_categories('orderby=name&order=asc&hierarchical=1&show_count=1&use_desc_for_title=0&hide_empty=0&depth=1&number=&title_li=&taxonomy='.APP_TAX_CAT); ?>
				</ul>

			</div><!-- /recordfromblog -->

		</div><!-- /shadowblock -->

	</div><!-- /shadowblock_out -->

<?php
}
$widget_ops = array( 'classname' => 'cp_ad_cats_widget', 'description' => "" );
wp_register_sidebar_widget( 'cp_ad_cats_widget_id', __( 'CP Ad Categories', APP_TD ), 'cp_ad_cats_widget', $widget_ops );


// widget to show the search widget
function cp_ad_search_widget() {
?>

	<div class="recordfromblog">

		<form action="<?php echo site_url('/'); ?>" method="get" id="searchform" class="form_search">

			<input name="s" type="text" id="s" class="editbox_search" <?php if(get_search_query()) { echo 'value="'.trim(strip_tags(esc_attr(get_search_query()))).'"'; } else { ?> value="<?php _e( 'What are you looking for?', APP_TD ); ?>" onfocus="if (this.value == '<?php _e( 'What are you looking for?', APP_TD ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'What are you looking for?', APP_TD ); ?>';}" <?php } ?> />

			<?php wp_dropdown_categories('show_option_all=' . __( 'All Categories', APP_TD ) . '&hierarchical='.get_option('cp_cat_hierarchy').'&hide_empty='.get_option('cp_cat_hide_empty').'&depth='.get_option('cp_search_depth').'&show_count='.get_option('cp_cat_count').'&pad_counts='.get_option('cp_cat_count').'&orderby=name&title_li=&use_desc_for_title=1&name=scat&selected='.cp_get_search_catid().'&taxonomy='.APP_TAX_CAT); ?>
			<div class="pad5"></div>
			<input type="submit" class="btn_orange" value="<?php _e( 'Search', APP_TD ); ?>" title="<?php _e( 'Search', APP_TD ); ?>" id="go" name="sa" />
		</form>

	</div><!-- /recordfromblog -->        

<?php
}
//$widget_ops = array('classname' => 'cp_ad_search_widget', 'description' => "" );
//wp_register_sidebar_widget('CP Ad Search', 'cp_ad_search_widget', $widget_ops);


// widget to show all categories excluding the blog cats
function cp_ad_region_widget() {
	global $wpdb;
?>
	<div class="shadowblock_out">

		<div class="shadowblock">

			<h2 class="dotted"><?php _e( 'Filter by City', APP_TD ); ?></h2>

			<div class="recordfromblog">

				<ul>
					<?php

						//$all_custom_fields = get_post_custom($post->ID);

						// get all the custom field labels so we can match the field_name up against the post_meta keys
						$sql = "SELECT field_values FROM $wpdb->cp_ad_fields f WHERE f.field_name = 'cp_city'";

						//$results = $wpdb->get_results($sql);
						$results = $wpdb->get_row( $sql );


						if ( $results ) {
					?>

							<a href="?region=all"><?php _e( 'All', APP_TD ); ?></a> /
							<?php
								$options = explode( ',', $results->field_values );

								foreach ( $options as $option ) {
							?>
									<a href="?region=<?php echo $option; ?>"><?php echo $option; ?></a> /
							<?php
								}

						} else {

							_e( 'No cities found.', APP_TD );

						}
					?>
				</ul>

			</div><!-- /recordfromblog -->

		</div><!-- /shadowblock -->

	</div><!-- /shadowblock_out -->

<?php
}

// not using this quite yet b/c query_posts isn't working with
// meta_key and meta_values (as of 2.9.2) even though it should be
// i.e. query_posts('meta_key=color&meta_value=blue');
//$widget_ops = array('classname' => 'cp_ad_region_widget', 'description' => "" );
//wp_register_sidebar_widget('cp_ad_region_widget_id', 'CP Ad Region Filter', 'cp_ad_region_widget', $widget_ops);



// custom sidebar 125x125 ads widget
class AppThemes_Widget_125_Ads extends WP_Widget {

	function AppThemes_Widget_125_Ads() {
		$widget_ops = array( 'description' => __( 'Places an ad space in the sidebar for 125x125 ads', APP_TD ) );
		$control_ops = array('width' => 500, 'height' => 350);
		$this->WP_Widget( 'cp_125_ads', __( 'CP 125x125 Ads', APP_TD ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$newin = isset( $instance['newin'] ) ? $instance['newin'] : false;


		if (isset($instance['ads'])) :

			// separate the ad line items into an array
			$ads = explode("\n", $instance['ads']);

			if (sizeof($ads)>0) :

				echo $before_widget;

				if ($title) echo $before_title . $title . $after_title;
				if ($newin) $newin = 'target="_blank"';
			?>

				<ul class="ads">
			<?php
				$alt = 1;
				foreach ($ads as $ad) :
					if ($ad && strstr($ad, '|')) {
						$alt = $alt*-1;
						$this_ad = explode('|', $ad);
						echo '<li class="';
						if ($alt==1) echo 'alt';
						echo '"><a href="'.$this_ad[0].'" rel="'.$this_ad[3].'" '.$newin.'><img src="'.$this_ad[1].'" width="125" height="125" alt="'.$this_ad[2].'" /></a></li>';
					}
				endforeach;
			?>
				</ul>

				<?php
				echo $after_widget;

			endif;

		endif;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['ads'] = strip_tags( $new_instance['ads'] );
		$instance['newin'] = $new_instance['newin'];

		return $instance;
	}

	function form( $instance ) {

		// load up the default values
		$default_ads = "http://www.appthemes.com|".get_bloginfo('template_url')."/images/ad125a.gif|Ad 1|nofollow\n"."http://www.appthemes.com|".get_bloginfo('template_url')."/images/ad125b.gif|Ad 2|follow\n"."http://www.appthemes.com|".get_bloginfo('template_url')."/images/ad125a.gif|Ad 3|nofollow\n"."http://www.appthemes.com|".get_bloginfo('template_url')."/images/ad125b.gif|Ad 4|follow";
		$defaults = array( 'title' => __( 'Sponsored Ads', APP_TD ), 'ads' => $default_ads, 'rel' => true, 'newin' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); 		
?>
		<p>
			<label><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label><?php _e( 'Ads:', APP_TD ); ?></label>
			<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('ads'); ?>" cols="5" rows="5"><?php echo $instance['ads']; ?></textarea>
			<?php _e( 'Enter one ad entry per line in the following format:<br /> <code>URL|Image URL|Image Alt Text|rel</code><br /><strong>Note:</strong> You must hit your &quot;enter/return&quot; key after each ad entry otherwise the ads will not display properly.', APP_TD ); ?>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['newin'], 'on'); ?> id="<?php echo $this->get_field_id('newin'); ?>" name="<?php echo $this->get_field_name('newin'); ?>" />
			<label><?php _e( 'Open ads in a new window?', APP_TD ); ?></label>
		</p>
<?php
	}
}


// facebook like box sidebar widget
class AppThemes_Widget_Facebook extends WP_Widget {

	function AppThemes_Widget_Facebook() {
		$widget_ops = array( 'description' => __( 'This places a Facebook page Like Box in your sidebar to attract and gain Likes from visitors.', APP_TD ) );
		$this->WP_Widget( 'cp_facebook_like', __( 'CP Facebook Like Box', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$fid = $instance['fid'];
		$connections = $instance['connections'];
		$width = $instance['width'];
		$height = $instance['height'];

		echo $before_widget;

		if ($title) echo $before_title . $title . $after_title;

	?>
		<div class="pad5"></div>
		<iframe src="http://www.facebook.com/plugins/likebox.php?id=<?php echo urlencode($fid); ?>&amp;connections=<?php echo urlencode($connections); ?>&amp;stream=false&amp;header=true&amp;width=<?php echo urlencode($width); ?>&amp;height=<?php echo $height; ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:<?php echo esc_attr($width); ?>px; height:<?php echo esc_attr($height); ?>px;" allowTransparency="true"></iframe>
		<div class="pad5"></div>
	<?php

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['fid'] = strip_tags( $new_instance['fid'] );
		$instance['connections'] = strip_tags($new_instance['connections']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['height'] = strip_tags($new_instance['height']);

		return $instance;
	}

	function form($instance) {

		$defaults = array( 'title' => __( 'Facebook Friends', APP_TD ), 'fid' => '137589686255438', 'connections' => '10', 'width' => '305', 'height' => '290' );
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('fid'); ?>"><?php _e( 'Facebook ID:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('fid'); ?>" name="<?php echo $this->get_field_name('fid'); ?>" value="<?php echo $instance['fid']; ?>" />
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('connections'); ?>" name="<?php echo $this->get_field_name('connections'); ?>" value="<?php echo $instance['connections']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('connections'); ?>"><?php _e( 'Connections', APP_TD ); ?></label>			
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e( 'Width', APP_TD ); ?></label>
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $instance['height']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e( 'Height', APP_TD ); ?></label>
		</p>

	<?php
	}
}


// twitter sidebar widget
class AppThemes_Widget_Twitter extends WP_Widget {

	function AppThemes_Widget_Twitter() {
		$widget_ops = array( 'description' => __( 'This places a real-time Twitter feed in your sidebar.', APP_TD ) );
		$this->WP_Widget( 'cp_twitter_widget', __( 'CP Real-Time Twitter Feed', APP_TD ), $widget_ops );

		if ( is_active_widget(false, false, $this->id_base, true) ) {
			add_action( 'wp_print_styles', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_print_scripts', array( $this, 'enqueue_scripts' ) );
		}

	}

	function widget( $args, $instance ) {

		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$tid = $instance['tid'];
		$api_key = $instance['api_key'];
		$keywords = strip_tags($instance['keywords']);
		$type = $instance['type'];
		$tcount = $instance['tcount'];
		$paging = $instance['paging'];
		$trefresh = $instance['trefresh'];
		$lang = $instance['lang'];
		$follow = isset($instance['follow']) ? $instance['follow'] : false;
		$connect = isset($instance['connect']) ? $instance['connect'] : false;

		echo $before_widget;

		if ($title) echo $before_title . $title . $after_title;
	?>
		
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#tweetFeed').jTweetsAnywhere({
					//searchParams: ['geocode=48.856667,2.350833,30km'],
				<?php if($type == 'username') { ?>
					username: '<?php echo esc_js($tid); ?>',
				<?php } else { ?>
					searchParams: ['q=<?php echo esc_js($keywords); ?>', 'lang=<?php echo $lang; ?>'],
				<?php } ?>
					count: <?php echo $tcount; ?>,
				<?php if($follow) echo "showFollowButton: true,"; ?>
				<?php if($connect) echo "showConnectButton: true,"; ?>
					showTweetFeed: {
						expandHovercards: true,
						showSource: true,
						paging: {
							mode: '<?php echo esc_js($paging); ?>'
						},
						showTimestamp: {
							refreshInterval: 30
						},
						autorefresh: {
							mode: '<?php echo esc_js($trefresh); ?>',
							interval: 20
						}

					},
					onDataRequestHandler: function(stats, options) {
						if (stats.dataRequestCount < 11) {
							return true;
						}
						else {
							stopAutorefresh(options);
							// alert("To avoid struggling with Twitter's rate limit, we stop loading data after 10 API calls.");
						}
					}


				});

			});
		</script>

		<div id="tweetFeed"></div>
		<div class="pad5"></div>

		<?php

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['tid'] = strip_tags($new_instance['tid']);
		$instance['api_key'] = strip_tags($new_instance['api_key']);
		$instance['keywords'] = strip_tags($new_instance['keywords']);
		$instance['type'] = $new_instance['type'];
		$instance['trefresh'] = $new_instance['trefresh'];
		$instance['tcount'] = strip_tags($new_instance['tcount']);
		$instance['paging'] = $new_instance['paging'];
		$instance['lang'] = strip_tags($new_instance['lang']);
		$instance['follow'] = $new_instance['follow'];
		$instance['connect'] = $new_instance['connect'];

		return $instance;
	}

	function form($instance) {

		$defaults = array(
				'title' => 'Twitter Updates',
				'tid' => 'appthemes',
				'api_key' => 'ZSO1guB57M6u0lm4cwqA',
				'keywords' => 'wordpress',
				'tcount' => '5',
				'type' => 'keyword',
				'paging' => 'prev-next',
				'trefresh' => 'trigger-insert',
				'follow' => '',
				'connect' => '',
				'lang' => 'en'
		);

		$instance = wp_parse_args((array) $instance, $defaults);
   ?>

		<p>
			<label><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label><?php _e( 'Twitter Username:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('tid'); ?>" name="<?php echo $this->get_field_name('tid'); ?>" value="<?php echo esc_attr( $instance['tid'] ); ?>" />
		</p>

		<p>
			<label><?php _e( 'Twitter API Key:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" value="<?php echo esc_attr( $instance['api_key'] ); ?>" />
		</p>

		<p>
			<label><?php _e( 'Keyword Tweets:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('keywords'); ?>" name="<?php echo $this->get_field_name('keywords'); ?>" value="<?php echo esc_attr( $instance['keywords']); ?>" />
		</p>

		<p>
			<label><?php _e( 'Display Type:', APP_TD ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" >
				<option value="username" <?php selected($instance['type'], 'username'); ?>><?php _e( 'Show Username Tweets', APP_TD ); ?></option>
				<option value="keywords" <?php selected($instance['type'], 'keywords'); ?>><?php _e( 'Show Keyword Tweets', APP_TD ); ?></option>
			</select>
		</p>

		<p>
			<label><?php _e( 'Refresh Mode:', APP_TD ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('trefresh'); ?>" name="<?php echo $this->get_field_name('trefresh'); ?>" >
				<option value="none" <?php selected($instance['trefresh'], 'none'); ?>><?php _e( 'None', APP_TD ); ?></option>
				<option value="auto-insert" <?php selected($instance['trefresh'], 'auto-insert'); ?>><?php _e( 'Real-Time Updates', APP_TD ); ?></option>
				<option value="trigger-insert" <?php selected($instance['trefresh'], 'trigger-insert'); ?>><?php _e( 'Click Button Updates', APP_TD ); ?></option>
			</select>
		</p>

		<p>
			<label><?php _e( 'Paging Style:', APP_TD ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('paging'); ?>" name="<?php echo $this->get_field_name('paging'); ?>" >
				<option value="more" <?php selected($instance['paging'], 'more'); ?>><?php _e( 'More Button', APP_TD ); ?></option>
				<option value="prev-next" <?php selected($instance['paging'], 'prev-next'); ?>><?php _e( 'Next &amp; Previous Buttons', APP_TD ); ?></option>
				<option value="endless-scroll" <?php selected($instance['paging'], 'endless-scroll'); ?>><?php _e( 'Endless Scrolling', APP_TD ); ?></option>
			</select>
		</p>

		<p>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('tcount'); ?>" name="<?php echo $this->get_field_name('tcount'); ?>" value="<?php echo esc_attr( $instance['tcount'] ); ?>" style="width:30px;" />
			<label for="<?php echo $this->get_field_id('tcount'); ?>"><?php _e( 'Tweets Shown', APP_TD ); ?></label>
		</p>

		<p>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" value="<?php echo esc_attr( $instance['lang'] ); ?>" style="width:30px;" />
			<label for="<?php echo $this->get_field_id('lang'); ?>"><?php _e( 'Default Language', APP_TD ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['follow'], 'on'); ?> id="<?php echo $this->get_field_id('follow'); ?>" name="<?php echo $this->get_field_name('follow'); ?>" />
			<label for="<?php echo $this->get_field_id('follow'); ?>"><?php _e( 'Show Follow Button', APP_TD ); ?></label>
			<br />
			<input class="checkbox" type="checkbox" <?php checked($instance['connect'], 'on'); ?> id="<?php echo $this->get_field_id('connect'); ?>" name="<?php echo $this->get_field_name('connect'); ?>" />
			<label for="<?php echo $this->get_field_id('connect'); ?>"><?php _e( 'Show Connect Button', APP_TD ); ?></label>
		</p>


	<?php
	}

	function enqueue_styles() {
		wp_register_style('jtweetsanywhere', get_bloginfo('template_directory').'/includes/js/jtweetsanywhere/jtweetsanywhere.css', false, '1.2.0');
		wp_enqueue_style('jtweetsanywhere');
	}

	function enqueue_scripts() {
		wp_enqueue_script('jtweetsanywhere', get_bloginfo('template_directory').'/includes/js/jtweetsanywhere/jtweetsanywhere.min.js', array('jquery'), '1.2.0');
		$options = $this->get_settings();
		$options = $options[$this->number];
		$options = wp_parse_args( $options, array( 'api_key' => 'ZSO1guB57M6u0lm4cwqA' ) );
		if ( !empty($options['api_key']) )
			wp_enqueue_script('twitter-anywhere', 'http://platform.twitter.com/anywhere.js?id=' . urlencode($options['api_key']) . '&v=1', array('jquery', 'jtweetsanywhere'), '1');
	}

}


// custom sidebar blog posts widget
class AppThemes_Widget_Blog_Posts extends WP_Widget {

	function AppThemes_Widget_Blog_Posts() {
		$widget_ops = array( 'description' => __( 'Your most recent blog posts', APP_TD ) );
		$this->WP_Widget( 'cp_recent_posts', __( 'CP Recent Blog Posts', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$count = $instance['count'];

		if ( !is_numeric($count) ) $count = 5;

		echo $before_widget;

		if ($title) echo $before_title . $title . $after_title;

		// include the main blog loop
		include(TEMPLATEPATH . '/includes/sidebar-blog-posts.php');

		echo $after_widget;

	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = (trim(strip_tags($new_instance['count'])));

		return $instance;
	}

	function form( $instance ) {

		// load up the default values
		$defaults = array( 'title' => __( 'From the Blog', APP_TD ), 'count' => 5 );
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<p>
			<label><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo $instance['count']; ?>" style="width:30px;" />
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Posts Shown', APP_TD ); ?></label>
		</p>


<?php
	}
}


// classipress sidebar search widget
class CP_Widget_Search extends WP_Widget {

	function CP_Widget_Search() {
		$widget_ops = array( 'description' => __( 'Your sidebar ad search box', APP_TD ) );
		$this->WP_Widget('ad_search', __( 'CP Ad Search Box', APP_TD ), $widget_ops);
	}

	function widget( $args, $instance ) {

		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? __( 'Search Classified Ads', APP_TD ) : $instance['title']);

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
		//echo '<div>';
		cp_ad_search_widget();
		//echo "</div>\n";
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		return $instance;
	}

	function form($instance) {
?>
    <p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ($instance['title'])) {echo esc_attr( $instance['title']);} ?>" />
		</p>
<?php
	}
}


// classipress sidebar top ads widget
class CP_Widget_Top_Ads_Today extends WP_Widget {

	function CP_Widget_Top_Ads_Today() {
		$widget_ops = array( 'description' => __( 'Your sidebar top ads today', APP_TD ) );
		$this->WP_Widget( 'top_ads', __( 'CP Top Ads Today', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? __( 'Popular Ads Today', APP_TD ) : $instance['title']);

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;

		cp_todays_count_widget(APP_POST_TYPE, 10);

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		return $instance;
	}

	function form($instance) {
?>
    <p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ($instance['title'])) {echo esc_attr( $instance['title']);} ?>" />
		</p>
<?php
	}
}


// classipress sidebar top ads widget
class CP_Widget_Top_Ads_Overall extends WP_Widget {

	function CP_Widget_Top_Ads_Overall() {
		$widget_ops = array( 'description' => __( 'Your sidebar top ads overall', APP_TD ) );
		$this->WP_Widget( 'top_ads_overall', __( 'CP Top Ads Overall', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? __( 'Popular Ads Overall', APP_TD ) : $instance['title']);

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		//echo '<div>';
		cp_todays_overall_count_widget(APP_POST_TYPE, 10);
		//echo "</div>\n";
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		return $instance;
	}

	function form($instance) {
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ($instance['title'])) {echo esc_attr( $instance['title']);} ?>" />
		</p>
<?php
	}
}


// widget to show all categories excluding the blog cats
// deprecated since 3.0.5
function cp_ad_sponsors_widget() {
?>

	<div class="shadowblock_out">

		<div class="shadowblock">

			<h2 class="dotted"><?php _e( 'Site Sponsors', APP_TD ); ?></h2>
			<a href="#"><img src="<?php bloginfo('template_url'); ?>/images/ad320.gif" width="307" height="96" alt="ad" class="fineborder ad320" /></a>
			<a href="#"><img src="<?php bloginfo('template_url'); ?>/images/ad125a.gif" width="125" height="125" alt="ad" class="fineborder ad125l" /></a>
			<a href="#"><img src="<?php bloginfo('template_url'); ?>/images/ad125b.gif" width="125" height="125" alt="ad" class="fineborder ad125r" /></a>

			<div class="clr"></div>

		</div><!-- /shadowblock -->

	</div><!-- /shadowblock_out -->

<?php
}
//$widget_ops = array('classname' => 'cp_ad_sponsors_widget', 'description' => "" );
//wp_register_sidebar_widget('cp_ad_sponsors_widget_id', 'CP Ad Sponsors', 'cp_ad_sponsors_widget', $widget_ops);


// ad tags and categories cloud widget
class CP_Widget_Ads_Tag_Cloud extends WP_Widget {

	function CP_Widget_Ads_Tag_Cloud() {
		$widget_ops = array( 'description' => __( 'Your most used ad tags in cloud format', APP_TD ) );
		$this->WP_Widget( 'ad_tag_cloud', __( 'CP Ads Tag Cloud', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract($args);
		$current_taxonomy = $this->_get_current_taxonomy($instance);
		if ( !empty($instance['title']) ) {
			$title = $instance['title'];
		} else {
			if ( 'ads_tag' == $current_taxonomy ) {
				$title = __( 'Ad Tags', APP_TD );
			} else {
				$tax = get_taxonomy($current_taxonomy);
				$title = $tax->labels->name;
			}
		}
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		echo '<div>';
		wp_tag_cloud( apply_filters('widget_tag_cloud_args', array('taxonomy' => $current_taxonomy) ) );
		echo "</div>\n";
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['taxonomy'] = stripslashes($new_instance['taxonomy']);
		return $instance;
	}

	function form( $instance ) {
		$current_taxonomy = $this->_get_current_taxonomy($instance);
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e( 'Taxonomy:', APP_TD ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>">
				<?php foreach ( get_object_taxonomies( APP_POST_TYPE ) as $taxonomy ) :
					$tax = get_taxonomy( $taxonomy );
					if ( !$tax->show_tagcloud || empty($tax->labels->name) )
						continue;
				?>
					<option value="<?php echo esc_attr($taxonomy); ?>" <?php selected($taxonomy, $current_taxonomy); ?>><?php echo $tax->labels->name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php
	}

	function _get_current_taxonomy($instance) {
		if ( !empty($instance['taxonomy']) && taxonomy_exists($instance['taxonomy']) )
			return $instance['taxonomy'];

		return 'post_tag';
	}
}


// register the custom sidebar widgets
function cp_widgets_init() {
	if ( !is_blog_installed() )
		return;

	register_widget('AppThemes_Widget_125_Ads');
	register_widget('AppThemes_Widget_Blog_Posts');
	register_widget('AppThemes_Widget_Twitter');
	register_widget('CP_Widget_Search');
	register_widget('CP_Widget_Top_Ads_Today');
	register_widget('CP_Widget_Top_Ads_Overall');
	register_widget('AppThemes_Widget_Facebook');
	register_widget('CP_Widget_Ads_Tag_Cloud');

	do_action('widgets_init');
}
add_action('init', 'cp_widgets_init', 1);


// remove some of the default sidebar widgets
function cp_unregister_widgets() {
	//unregister_widget('WP_Widget_Pages');
	//unregister_widget('WP_Widget_Calendar');
	//unregister_widget('WP_Widget_Archives');
	//unregister_widget('WP_Widget_Links');
	//unregister_widget('WP_Widget_Categories');
	//unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Search');
	//unregister_widget('WP_Widget_Tag_Cloud');
}
add_action('widgets_init', 'cp_unregister_widgets');

?>