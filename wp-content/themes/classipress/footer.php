<div class="footer">

		<div class="footer_menu">

				<div class="footer_menu_res">

						<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'container' => false, 'menu_id' => 'footer-nav-menu', 'depth' => 1, 'fallback_cb' => false ) ); ?>

						<div class="clr"></div>

				</div><!-- /footer_menu_res -->

		</div><!-- /footer_menu -->

		<div class="footer_main">

				<div class="footer_main_res">

						<div class="dotted">

								<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_footer') ) : else : ?> <!-- no dynamic sidebar so don't do anything --> <?php endif; ?>

								<div class="clr"></div>

						</div><!-- /dotted -->

						<p>&copy; <?php echo date_i18n('Y'); ?> <?php bloginfo('name'); ?>. <?php _e( 'All Rights Reserved.', APP_TD ); ?></p>

						<?php if ( get_option('cp_twitter_username') ) : ?>
								<a href="http://twitter.com/<?php echo get_option('cp_twitter_username'); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/twitter_bot.gif" width="42" height="50" alt="Twitter" class="twit" /></a>
						<?php endif; ?>

						<div class="right">
								<p><a target="_blank" href="http://www.mafiashare.net" title="Classified Ads Software"><?php _e( 'Classified Ads Software', APP_TD ); ?></a> | <?php _e( 'Powered by', APP_TD ); ?> <a target="_blank" href="http://www.mafiashare.net" title="WordPress">WordPress</a></p>
						</div>

						<div class="clr"></div>

				</div><!-- /footer_main_res -->

		</div><!-- /footer_main -->

</div><!-- /footer -->