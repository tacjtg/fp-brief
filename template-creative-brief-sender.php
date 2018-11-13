<?php
/**
 * Template Name: Creative Brief Sender
 */
fp_archive_header();
acf_form_head();
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
					<div class="fp-creative-brief-sender">
					<?php
						$acf_current_post = array(
							'post_id'		=> 'brief_sender',
							'field_groups'	=> array('group_5bb6188159cf0'), 
							'form'			=> true,
							'return'		=> '%post_url%',
							'submit_value'	=> 'Send Creative Briefs',
							'post_title'	=> false,
							'post_content'	=> false,
						);
						acf_form ( $acf_current_post );
					?>
					</div><!-- .fp-creative-brief-sender -->
				</div><!-- .col -->				
			</div><!-- .row -->
		</div><!-- .container-fluid -->
	</div><!-- .bootstrap-wrapper -->
</div><!-- .fp-container -->	
<a href="#" class="topbutton"></a>
<?php get_footer();