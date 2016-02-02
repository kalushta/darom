<?php
/**
 * Template Name: Ads Home Template
 *
 * @package ClassiPress\Templates
 * @author  AppThemes
 * @since   ClassiPress 3.3
 */
?>


<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<?php get_template_part( 'featured' ); ?>

			<!-- left block -->
			<div class="content_left">


				<?php if ( $cp_options->home_layout == 'directory' ) { ?>

					<div class="shadowblock_out">

						<div class="shadowblock">

							<h2 class="dotted"><?php _e( 'Ad Categories', APP_TD ); ?></h2>

							<div id="directory" class="directory <?php cp_display_style( 'dir_cols' ); ?>">

								<?php echo cp_create_categories_list( 'dir' ); ?>

								<div class="clr"></div>

							</div><!--/directory-->

						</div><!-- /shadowblock -->

					</div><!-- /shadowblock_out -->

				<?php } ?>


				<div class="tabcontrol">

					<ul class="tabnavig">
						<li><a href="#block1"><span class="big"><?php _e( 'New Listings', APP_TD ); ?></span></a></li>
						<li><a href="#block2" id="popular" class="dynamic-content"><span class="big"><?php _e( 'Popular', APP_TD ); ?></span></a></li>
						<li><a href="#block3" id="random" class="dynamic-content"><span class="big"><?php _e( 'Random', APP_TD ); ?></span></a></li>
					</ul>

					<?php
						remove_action( 'appthemes_after_endwhile', 'cp_do_pagination' );
						$post_type_url = add_query_arg( array( 'paged' => 2 ), get_post_type_archive_link( APP_POST_TYPE ) );
					?>

					<!-- tab 1 -->
					<div id="block1">

						<div class="clr"></div>

						<?php
							// show all ads but make sure the sticky featured ads don't show up first
							$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
							query_posts( array( 'post_type' => APP_POST_TYPE, 'ignore_sticky_posts' => 1, 'paged' => $paged ) );
							$total_pages = max( 1, absint( $wp_query->max_num_pages ) );
						?>

						<?php get_template_part( 'loop', 'ad_listing' ); ?>

						<?php
							if ( $total_pages > 1 ) {
								cp_the_view_more_ads_link( $post_type_url );
							}
						?>

					</div><!-- /block1 -->


					<!-- tab 2 -->
					<div id="block2">

						<div class="clr"></div>

						<div class="post-block-out post-block popular-placeholder"><!-- dynamically loaded content --></div>

					</div><!-- /block2 -->


					<!-- tab 3 -->
					<div id="block3">

						<div class="clr"></div>

						<div class="post-block-out post-block random-placeholder"><!-- dynamically loaded content --></div>

					</div><!-- /block3 -->


				</div><!-- /tabcontrol -->

			</div><!-- /content_left -->


			<?php get_sidebar(); ?>


			<div class="clr"></div>

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
