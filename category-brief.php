<?php
/**
 * Archive: Creative Briefs
 */
fp_archive_header();
get_header(); ?>
<div class="fp-container">
	<div class="bootstrap-wrapper">
		<div class="container-fluid">			
			<div class="row">			
				<div class="col-lg-3 col-md-4"></div><!-- .col -->
				<div class="col-lg-9 col-md-8">
					<h1>Creative Briefs</h1>
				</div><!-- .col -->
			</div><!-- .row -->			
			<div class="row">			
				<div class="col-lg-3 col-md-4">
					<div class="fp-brief-navbox">																							
						<?php if ( is_active_sidebar( 'fp-brief' ) ) : ?>
							<h3>Projects</h3>
							<ul id="fp-brief-sidebar">
								<?php dynamic_sidebar( 'fp-brief' ); ?>
								<a href="<?php echo get_post_type_archive_link( 'fp-brief' ); ?>" class="fp-brief-btn">All Briefs</a>
							</ul>
						<?php endif; ?>
						<hr>
						<a class="fp-brief-btn" href="http://dvlseigenthaler.com/media/creative-brief-sender/">Brief Sender</a>									
					</div><!-- .fp-brief-navbox -->
				</div><!-- .col -->				
				<div class="col-lg-9 col-md-8">				
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>					
						<div class="fp-brief-archive-listing" <?php fp_brief_border_color(); ?>>							
							<div class="row">
								<div class="col">
									<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
								</div><!-- .col -->		
							</div><!-- .row -->							
							<div class="row mt-2">
								<div class="col-sm-6">
									<div class="fp-brief-detail"><?php fp_brief_finalized(); ?> on: <span class="fp-brief-detail-text"><?php the_field('fp_date'); ?></span></div>
								</div>
								<div class="col-sm-6">
									<div class="fp-brief-detail"><?php fp_brief_finalized(); ?> by: <span class="fp-brief-detail-text"><?php the_field('fp_author'); ?></span></div>
								</div><!-- .col -->			
							</div><!-- .row -->							
						</div><!-- .fp-brief-archive-listing -->					
					<?php endwhile; else : ?>		
						<div class="row">
							<div class="col">
								<p><?php esc_html_e( 'Sorry, no creative briefs.' ); ?></p>
							</div><!-- .col -->			
						</div><!-- .row -->	
					<?php endif; ?>
				</div><!-- .col -->				
			</div><!-- .row -->
		</div><!-- .container-fluid -->
	</div><!-- .bootstrap-wrapper -->
</div><!-- .fp-container -->	
<a href="#" class="topbutton"></a>
<?php get_footer();