<?php
/**
 * Single Creative Brief
 */
acf_form_head();
wp_deregister_style( 'wp-admin' );
get_header();
if ( current_user_can('administrator') ) { // BEGIN - If user is admin ?>
	<div class="fp-container">
		<div class="bootstrap-wrapper">
			<div class="container-fluid">			
				<div class="row">			
					<div class="col-lg-3 col-md-4"></div><!-- .col -->
					<div class="col-lg-9 col-md-8">
						<h1><?php the_field('fp_project');?></h1>
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
							<a id="edit-post" class="fp-brief-btn" href="#edit">Edit This Brief</a>							
							<a class="fp-brief-btn" href="http://dvlseigenthaler.com/media/?export=csv">Export This Brief (.csv)</a>
							<hr>
							<a id="create-post" class="fp-brief-btn" href="#create-post">Create Finalized Brief</a>
							<hr>
							<a class="fp-brief-btn" href="http://dvlseigenthaler.com/media/creative-brief-sender/">Brief Sender</a>					
						</div><!-- .fp-brief-navbox -->
					</div><!-- .col -->				
					<div class="col-lg-9 col-md-8">
						<div class="fp-brief-client-info" <?php fp_brief_border_color();?>>
							<div class="row">											
								<div class="col-sm-6">
									<div class="fp-brief-detail">Client: <span class="fp-brief-detail-text"><?php the_field('fp_client'); ?></span></div>
									<div class="fp-brief-detail">Project: <span class="fp-brief-detail-text"><?php the_field('fp_project'); ?></span></div>
								</div><!-- .col -->	
								<div class="col-sm-6">
									<div class="fp-brief-detail">Date: <span class="fp-brief-detail-text"><?php the_field('fp_date'); ?></span></div>
									<div class="fp-brief-detail"><?php fp_brief_finalized(); ?> by: <span class="fp-brief-detail-text"><?php the_field('fp_author'); ?></span></div>
								</div><!-- .col -->	
							</div><!-- .row -->
						</div><!-- .fp-brief-client-info -->					
						<div class="row">
							<div class="col">
								<div class="fp-brief-questions">
									<div class="fp-brief-question">1. What is the objective?</div>
									<div class="fp-brief-answer"><?php the_field('fp_objective'); ?></div>						
									<div class="row"><?php fp_brief_additional_content( 'fp_objective' ); ?></div>
														
									<div class="fp-brief-question">2. To whom are we talking?</div>
									<div class="fp-brief-answer"><?php the_field('fp_audience'); ?></div>
									<div class="row"><?php fp_brief_additional_content( 'fp_audience' ); ?></div>
										
									<div class="fp-brief-question">3. What is the single most persuasive thought we need to convey?</div>
									<div class="fp-brief-answer"><?php the_field('fp_persuasive'); ?></div>
									<div class="row"><?php fp_brief_additional_content( 'fp_persuasive' ); ?></div>
										
									<div class="fp-brief-question">4. Why should the target believe this?</div>
									<div class="fp-brief-answer"><?php the_field('fp_believe'); ?></div>
									<div class="row"><?php fp_brief_additional_content( 'fp_believe' ); ?></div>
										
									<div class="fp-brief-question">5. What do we want them to think/feel/do as a result of this communication?</div>
									<div class="fp-brief-answer"><?php the_field('fp_want'); ?></div>
									<div class="row"><?php fp_brief_additional_content( 'fp_want' ); ?></div>
										
									<div class="fp-brief-question">6. What does the target currently think/feel/do?</div>
									<div class="fp-brief-answer"><?php the_field('fp_think'); ?></div>
									<div class="row"><?php fp_brief_additional_content( 'fp_think' ); ?></div>
										
									<div class="fp-brief-question">7. What should be the tone of the communications?</div>
									<div class="fp-brief-answer"><?php the_field('fp_tone'); ?></div>
									<div class="row"><?php fp_brief_additional_content( 'fp_tone' ); ?></div>
										
									<div class="fp-brief-question">8. Is there anything mandatory that we do or include?</div>
									<div class="fp-brief-answer"><?php the_field('fp_mandatories'); ?></div>
									<div class="row"><?php fp_brief_additional_content( 'fp_mandatories' ); ?></div>
										
									<div class="fp-brief-question">9. Other key audience insights?</div>
									<div class="fp-brief-answer"><?php the_field('fp_insights'); ?></div>
									<div class="row"><?php fp_brief_additional_content( 'fp_insights' ); ?></div>								
								</div><!-- .fp-brief-questions -->
							</div><!-- .col -->
						</div><!-- .row -->							
					</div><!-- .col -->
				</div><!-- .row -->
			</div><!-- .container-fluid -->
		</div><!-- .bootstrap-wrapper -->
	</div><!-- .fp-container -->
	
	<!-- BEGIN Left Sidr -->
	<div id='sidr-left' class='acf-edit-post'>
	<a href='#' class='fp-brief-btn edit-close'>Close Editor</a>
	<?php
		$acf_current_post = array(
			'field_groups' => array('group_5ba24f1c4bfcf'), 
			'form' => true,
			'return' => '%post_url%',
			'submit_value' => 'Save Changes',
			'post_title' => false,
			'post_content' => false,
		);
		acf_form ( $acf_current_post );
	?>
	</div>
	<!-- END Left Sidr -->
	
	<!-- BEGIN Right Sidr -->
	<div id='sidr-right' class='acf-edit-post'>
	<a href='#' class='fp-brief-btn create-close'>Close Editor</a>
	<?php
		$acf_new_post = array(
			'post_id' => 'new',
			'field_groups' => array('group_5ba24f1c4bfcf'), 
			'form' => true,
			'return' => '%post_url%', 
			'html_before_fields' => '',
			'html_after_fields' => '',
			'submit_value' => 'Submit Brief',
		);
		acf_form( $acf_new_post );
	?>
	</div>
	<!-- END Right Sidr -->
	
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#edit-post').sidr({
		      name: 'sidr-left',
		      side: 'left'
		    });
		    jQuery( '.edit-close' ).on( 'click', function() {
		        jQuery.sidr( 'close' , 'sidr-left' );
		    });
		    jQuery('#create-post').sidr({
		      name: 'sidr-right',
		      side: 'right'
		    });	    
		    jQuery( '.create-close' ).on( 'click', function() {
		        jQuery.sidr( 'close' , 'sidr-right' );
		    });
		});
	</script>
	
<?php } // END - If user is admin	
		elseif ( current_user_can('editor') ) { // BEGIN - If user is editor ?>
<div class="fp-container" style="max-width: 800px;margin: 0 auto;">
	<div class="bootstrap-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col">				
					<div class="fp-creative-brief-form">
						<?php			 
							$acf_client_post = array(
								'field_groups' => array('group_5ba24f1c4bfcf'), 
								'form' => true,
								'return' => add_query_arg( 'updated', 'true', get_permalink() ),
								'submit_value' => 'Update',
								'post_title' => false,
								'post_content' => false,
								'updated_message' => 'Creative Brief Updated. Thank you!'
							);
							acf_form($acf_client_post);
						?>
					</div><!-- .fp-creative-brief-form -->
				</div><!-- .col -->	
			</div><!-- .row -->
		</div><!-- .container-fluid -->
	</div><!-- .bootstrap-wrapper -->
</div><!-- .fp-container -->
<?php } // END - If user is editor
		else { // BEGIN - If user is logged out ?>
	<div class="fp-container" style="max-width: 800px;margin: 0 auto;">
	<div class="bootstrap-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col">
					<div class="fp-creative-brief-client-login">
						<script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery("#user_login").val('creativebrief');    
								jQuery("#user_pass").val('!oKIsAFP$N1x800U6acjqKpS');
							});
						</script>							
						<?php 
							$login_args = array(
								'remember'       => false,
								'label_username' => __( '' ),
								'label_password' => __( '' ),
								'label_remember' => __( '' ),
								'label_log_in'   => __( 'Click Here Edit The Creative Brief' ),
							);							
							wp_login_form( $login_args );
						?>
					</div>					
				</div><!-- .col -->	
			</div><!-- .row -->
		</div><!-- .container-fluid -->
	</div><!-- .bootstrap-wrapper -->
</div><!-- .fp-container -->
<?php } // END - If user is logged out ?>

<a href="#" class="topbutton"></a>
<?php get_footer();