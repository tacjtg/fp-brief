<?php
/**
 * Template Name: Creative Brief Form
 */
acf_form_head();
get_header(); ?>
<div class="fp-container" style="max-width: 800px;margin: 0 auto;">
	<div class="bootstrap-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col">
					<div class="fp-creative-brief-form">
						<?php			 
							$new_post = array(
								'post_id' => 'new', // Create a new post
								'field_groups' => array('group_5ba24f1c4bfcf'), // Create post field group ID(s)
								'form' => true,
								'return' => '%post_url%', // Redirect to new post url
								'html_before_fields' => '',
								'html_after_fields' => '<input type="text" id="catchingbadguys" name="catchingbadguys" autocomplete="off">',
								'submit_value' => 'Submit Creative Brief',
								'updated_message' => 'Saved!'
							);
							acf_form( $new_post );
						?>
					</div><!-- .fp-creative-brief-form -->
				</div><!-- .col -->	
			</div><!-- .row -->
		</div><!-- .container-fluid -->
	</div><!-- .bootstrap-wrapper -->
</div><!-- .fp-container -->
<?php get_footer();