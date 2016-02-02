<div class="header">

		<div class="header_top">

				<div class="header_top_res">

						<p>
								<?php echo cp_login_head(); ?>

								<a href="<?php echo appthemes_get_feed_url(); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/icon_rss.gif" width="16" height="16" alt="rss" class="srvicon" /></a>

								<?php if ( get_option('cp_twitter_username') ) : ?>
										&nbsp;|&nbsp;<a href="http://twitter.com/<?php echo get_option('cp_twitter_username'); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/icon_twitter.gif" width="16" height="16" alt="tw" class="srvicon" /></a>
								<?php endif; ?>
						</p>

				</div><!-- /header_top_res -->

		</div><!-- /header_top -->


		<div class="header_main">

				<div class="header_main_bg">

						<div class="header_main_res">

								<div id="logo">

										<?php if ( get_option('cp_use_logo') != 'no' ) { ?>

												<?php if ( get_option('cp_logo') ) { ?>
														<a href="<?php echo home_url(); ?>"><img src="<?php echo get_option('cp_logo'); ?>" alt="<?php bloginfo('name'); ?>" class="header-logo" /></a>
												<?php } else { ?>
														<a href="<?php echo home_url(); ?>"><div class="cp_logo"></div></a>
												<?php } ?>

										<?php } else { ?>

												<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
												<div class="description"><?php bloginfo('description'); ?></div>

										<?php } ?>

								</div><!-- /logo -->

								<div class="adblock">
									<?php appthemes_advertise_header(); ?>
								</div><!-- /adblock -->

								<div class="clr"></div>

						</div><!-- /header_main_res -->

				</div><!-- /header_main_bg -->

		</div><!-- /header_main -->


		<div class="header_menu">

				<div class="header_menu_res">

                <a href="<?php echo CP_ADD_NEW_URL; ?>" class="obtn btn_orange"><?php _e( 'Post an Ad', APP_TD ); ?></a>

                <?php wp_nav_menu( array('theme_location' => 'primary', 'fallback_cb' => false, 'container' => false) ); ?>

                <div class="clr"></div>

    
				</div><!-- /header_menu_res -->

		</div><!-- /header_menu -->

</div><!-- /header -->