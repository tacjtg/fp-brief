<?php
/*
Plugin Name: Finn Partners Creative Brief Plugin
Plugin URI: 
Description: Client Creative Brief
Version: 1.0
Author: JTG
Author URI: https://jonathangatlin.com
License: GPLv2
*/

class FP_Brief_Plugin {
	
	public function __construct() {
		
		if( ! class_exists('Agp_Autoloader') ) {  
        	include_once( plugin_dir_path( __FILE__ ) . 'vendor/agp-ajax-taxonomy-filter/agp-ajax-taxonomy-filter.php' );
        }
		
		if( ! class_exists('acf') ) {     
        	include_once( plugin_dir_path( __FILE__ ) . 'vendor/acf-pro/acf.php' );
        }
		
		add_filter( 'acf/settings/path', array( $this, 'update_acf_settings_path' ) );
        add_filter( 'acf/settings/dir', array( $this, 'update_acf_settings_dir' ) );
        add_filter( 'acf/settings/show_admin', '__return_false' );
        add_filter( 'acf/pre_save_post' , array( $this, 'fp_brief_save_post' ) );			 
        add_filter( 'acf/pre_save_post' , array( $this, 'fp_brief_save_sender' ) );			
		add_action( 'init', array( $this, 'register_fp_brief' ) ); 	
		add_action( 'init', array( $this, 'fp_brief_options' ) );
		add_action( 'init', array( $this, 'fp_brief_sender_options' ) );
		add_action( 'init', array( $this, 'fp_brief_taxonomies' ) ); 
		add_action( 'init', array( $this, 'fp_brief_sidebar' ) ); 						
		add_action( 'wp_enqueue_scripts', array( $this, 'fp_brief_frontend_styles' ), 99 ); 			
		add_filter( 'archive_template', array( $this, 'fp_brief_archive_template' ) ); 
		add_filter( 'archive_template', array( $this, 'fp_brief_category_template' ) ); 		
		add_filter( 'single_template', array( $this, 'fp_brief_single_template' ) ); 				
		add_filter( 'theme_page_templates', array( $this, 'add_new_template' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'register_project_templates' ) );
		add_filter( 'template_include',  array( $this, 'view_project_template' ) );
	
		$this->templates = array(	'template-creative-brief-form.php' 		=> 'Creative Brief Form',
									'template-creative-brief-sender.php' 	=> 'Creative Brief Sender');
				
		error_reporting(E_ALL); ini_set('display_errors', 1);
				
		function fp_brief_finalized() {
			global $post;
			$post_id = $post->ID;
			$editor = get_the_modified_author( $post_id );
			$editor_id = get_post_meta($post_id, '_edit_last', true);
			$editor_role = get_the_author_meta('user_level', $editor_id);		
			if ( $editor_role == '10' ) {
				echo 'Finalized';
			} else { 
				echo 'Submitted';
			}
		} // Submitted or Finalized
		
		function fp_brief_border_color() {
			global $post;
			$post_id = $post->ID;
			$editor = get_the_modified_author( $post_id );
			$editor_id = get_post_meta($post_id, '_edit_last', true);
			$editor_role = get_the_author_meta('user_level', $editor_id);				
			if ( $editor_role == '10' ) {
				$border = '#ae162d';
			} else { 
				$border = '#dee2e6';
			}
			echo 'style="border: 1px solid ' . $border . '"';
		} // Finalized have red border		
				
		function fp_brief_additional_content( $content_field ) {
			
			if ( is_user_logged_in() ) {
		
				global $post;
				$current_post_id = $post->ID;
				$terms = get_the_terms( $post->ID, 'fp_brief_projects');
					
				if ( empty( $terms ) ){
					return;
				} else {
					$term = array_shift( $terms );
					$term_slug = $term->slug;
				}
			
				$args = array(
					'post_type' => 'fp-brief',
					'post__not_in' => array($current_post_id),
					'posts_per_page' => 10,
					'tax_query' => array(
										array(
											'taxonomy' => 'fp_brief_projects',
											'field'    => 'slug',
											'terms'    => $term_slug,
										),
									), 
					);
						
				$loop = new WP_Query( $args );
				while ( $loop->have_posts() ) : $loop->the_post();
						
					$author = get_field('fp_author');
					$link = get_the_permalink($post->ID);
						
					echo '<div class="col-sm-6">';
						echo '<div class="fp-additional-content">';
							echo '<div class="fp-additional-content-author">From: ';
								echo '<a href="' . $link . '" target="_blank">';
									echo '<span class="fp-additional-content-author-text">' . $author .'</span>';
								echo '</a>';
							echo '</div>';
							the_field($content_field);
						echo '</div>';
					echo '</div>';
						
				endwhile;
				wp_reset_query();
			
			} else {
				return;
			}		
		} // Loop for additional content in same taxonomy
		
		function fp_archive_header(){
			if ( !is_user_logged_in() ) {
			    	wp_redirect( wp_login_url() ); exit;
			}
		} // Archives require login
    }
	
	// Include ACF
	
	public function update_acf_settings_path( $path ) {
	   $path = plugin_dir_path( __FILE__ ) . 'vendor/acf-pro/';
	   return $path;
	}
    
    public function update_acf_settings_dir( $dir ) {
        $dir = plugin_dir_url( __FILE__ ) . 'vendor/acf-pro/';
        return $dir;
    }
    
    // Register CPTs
       
    public function register_fp_brief() {
		$labels = array(
			"name" => __( 'Creative Briefs', 'fpbriefplugin' ),
			"singular_name" => __( 'Creative Brief', 'fpbriefplugin' ),
			);
		$args = array(
			"label" => __( 'Creative Brief', 'fpbriefplugin' ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => true,
			"show_in_menu" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "creative-briefs", "with_front" => true ),
			"query_var" => true,
			"menu_icon" => "dashicons-media-document",
			"supports" => array( 'title' ),
		);
		register_post_type( "fp-brief", $args );
	}
    
    // Register Taxonomies	
    
	public function fp_brief_taxonomies() {		
		register_taxonomy(  
	        'fp_brief_projects',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces). 
	        'fp-brief',        //post type name
	        array(  
	            'hierarchical' => true,  
	            'label' => 'Projects',  //Display name
	            'query_var' => true,
	            'rewrite' => array(
	                'slug' => 'creative-brief-projects', // This controls the base slug that will display before each term
	                'with_front' => false // Don't display the category base before 
	            )
	        )  
	    );
	}
	
	// Include ACF Options	
	
	public function fp_brief_options() {
		if( function_exists('acf_add_local_field_group') ):
			acf_add_local_field_group(array(
				'key' => 'group_5ba24f1c4bfcf',
				'title' => 'Creative Brief',
				'fields' => array(
					array(
						'key' => 'field_5ba250aeba333',
						'label' => 'Client',
						'name' => 'fp_client',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '50',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5ba250efba334',
						'label' => 'Project',
						'name' => 'fp_project',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '50',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5ba25087ba332',
						'label' => 'Date',
						'name' => 'fp_date',
						'type' => 'date_picker',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '50',
							'class' => '',
							'id' => '',
						),
						'display_format' => 'm/d/Y',
						'return_format' => 'm/d/Y',
						'first_day' => 1,
					),
					array(
						'key' => 'field_5babed871754f',
						'label' => 'Your Name',
						'name' => 'fp_author',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '50',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5ba24f29ea383',
						'label' => '1. What is the objective?',
						'name' => 'fp_objective',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_5ba24f9eea384',
						'label' => '2. To whom are we talking?',
						'name' => 'fp_audience',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_5ba24fb2ea385',
						'label' => '3. What is the single most persuasive thought we need to convey?',
						'name' => 'fp_persuasive',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_5ba24fdaea386',
						'label' => '4. Why should the target audience believe this?',
						'name' => 'fp_believe',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_5ba2500eea387',
						'label' => '5. What do we want them to think/feel/do as a result of this communication?',
						'name' => 'fp_want',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_5ba2501eea388',
						'label' => '6. What does the target currently think/feel/do?',
						'name' => 'fp_think',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_5ba25034ea389',
						'label' => '7. What should be the tone of the communications?',
						'name' => 'fp_tone',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_5ba25043ea38a',
						'label' => '8. Is there anything mandatory that we do or include?',
						'name' => 'fp_mandatories',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_5ba25059ea38b',
						'label' => '9. Other key audience insights?',
						'name' => 'fp_insights',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'fp-brief',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => 1,
				'description' => '',
			));
			
			endif;
	}
	
	public function fp_brief_sender_options() {
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_5bb6188159cf0',
	'title' => 'Creative Brief Sender',
	'fields' => array(
		array(
			'key' => 'field_5bb6196d3a3ee',
			'label' => 'Client',
			'name' => 'fp_brief_sender_client',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5bb6197b3a3ef',
			'label' => 'Project',
			'name' => 'fp_brief_sender_project',
			'type' => 'taxonomy',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => 'fp_brief_projects',
			'field_type' => 'select',
			'allow_null' => 0,
			'add_term' => 1,
			'save_terms' => 1,
			'load_terms' => 0,
			'return_format' => 'object',
			'multiple' => 0,
		),
		array(
			'key' => 'field_5bb619b225a25',
			'label' => 'Creative Brief Recipients',
			'name' => 'fp_brief_sender_recipients',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'collapsed' => '',
			'min' => 0,
			'max' => 0,
			'layout' => 'table',
			'button_label' => 'Add Recipient',
			'sub_fields' => array(
				array(
					'key' => 'field_5bb619ff25a26',
					'label' => 'Name',
					'name' => 'fp_brief_sender_recipient_name',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5bb61a2b25a27',
					'label' => 'Email',
					'name' => 'fp_brief_sender_recipient_email',
					'type' => 'email',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'page_template',
				'operator' => '==',
				'value' => 'template-creative-brief-sender.php',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

endif;
	}
	
	// Create new ACF Title
	
	public function fp_brief_title_updater( $post_id ) {

	    $my_post = array();
	    $my_post['ID'] = $post_id;
	
	    $new_post_title = $_POST['acf']['field_5ba250aeba333'];
		$new_post_title .= ' - ';
		$new_post_title .= $_POST['acf']['field_5ba250efba334'];
	
		$my_post['post_title'] = $new_post_title;

		wp_update_post( $my_post );
	}
	
	// Save ACF Post Data	
	
	public function fp_brief_save_post( $post_id ) {
 
		//if ( ! ( is_user_logged_in() || current_user_can('publish_posts') ) ) { return; }
		
		if ( $_POST[ 'catchingbadguys' ] != '' ) { die( "You spammer!" ); } // Honeypot
		
		if ( $post_id != 'new' ) { return $post_id; }
		
		// Create New Post Title 'Client - Project'
		$new_post_title = $_POST['acf']['field_5ba250aeba333'];
		$new_post_title .= ' - ';
		$new_post_title .= $_POST['acf']['field_5ba250efba334'];
		
		// Create a new post
		$post = array(
			'post_type' => 'fp-brief', // Your post type ( post, page, custom post type )
			'post_status' => 'publish', // You can use -> publish, draft, private, etc.
			'post_title' => wp_strip_all_tags($new_post_title), // Post Title ACF field key
		);
		
		$post_id = wp_insert_post( $post );
		 
		// Save the fields to the post
		do_action( 'acf/save_post' , $post_id );
		 
		return $post_id;	 
	}
	
	public function fp_brief_save_client( $post_id ) {
		
		if ( $post_id != 'client' ) { return $post_id; }
		
		$client			= $_POST['acf']['field_5ba250aeba333']; 
		$project		= $_POST['acf']['field_5ba250efba334'];
		$date			= $_POST['acf']['field_5ba25087ba332'];
		$author			= $_POST['acf']['field_5babed871754f'];
		
		$question1		= $_POST['acf']['field_5ba24f29ea383'];
		$question2		= $_POST['acf']['field_5ba24f9eea384'];
		$question3		= $_POST['acf']['field_5ba24fb2ea385'];
		$question4		= $_POST['acf']['field_5ba24fdaea386'];
		$question5		= $_POST['acf']['field_5ba2500eea387'];
		$question6		= $_POST['acf']['field_5ba2501eea388'];
		$question7		= $_POST['acf']['field_5ba2501eea389'];
		$question8		= $_POST['acf']['field_5ba25043ea38a'];
		$question9		= $_POST['acf']['field_5ba25059ea38b'];

		do_action( 'acf/save_post' , $post_id );
		
		update_field( 'fp_client' 		, $client		, $post_id);
		update_field( 'fp_project' 		, $project		, $post_id);
		update_field( 'fp_date' 		, $date			, $post_id);
		update_field( 'fp_author' 		, $author		, $post_id);
		update_field( 'fp_objective' 	, $question1	, $post_id);
		update_field( 'fp_audience' 	, $question2	, $post_id);
		update_field( 'fp_persuasive' 	, $question3	, $post_id);
		update_field( 'fp_believe' 		, $question4	, $post_id);
		update_field( 'fp_want' 		, $question5	, $post_id);
		update_field( 'fp_think' 		, $question6	, $post_id);
		update_field( 'fp_tone' 		, $question7	, $post_id);
		update_field( 'fp_mandatories' 	, $question8	, $post_id);
		update_field( 'fp_insights' 	, $question9	, $post_id);
		 
		return $post_id;	 
	}
	
	public function fp_brief_save_sender( $post_id ) {
 
		if ( ! is_user_logged_in() ) { return; }
		
		if ( $post_id != 'brief_sender' ) { return $post_id; }
		
		$values = $_POST['acf']['field_5bb619b225a25'];
			
		foreach ( $values as $value ) {

			$client			= $_POST['acf']['field_5bb6196d3a3ee']; 
			$project		= $_POST['acf']['field_5bb6197b3a3ef']; 
			$recip_name		= $value['field_5bb619ff25a26']; 
			$recip_email	= $value['field_5bb61a2b25a27'];
	
			// Create New Post Title 'Client - Project'
			$project_term 	= get_term( $project, 'fp_brief_projects' );
			$project_name 	= $project_term->name;
			$new_post_title = $client . ' - ' . $project_name;
			
			// Create Post 
		    $post = array(
		      'post_status'	=> 'publish',
		      'post_title' 	=> wp_strip_all_tags($new_post_title), 
		      'post_type'	=> 'fp-brief'
		    );
		    
		    $post_id = wp_insert_post( $post );
		    
		    // Email link to recipient
		    $to = $recip_email;
		    $link = get_permalink( $post_id  );
		    $subject = 'Finn Partners Creative Brief';
			$headers = array(
							'From: Jimmy Chaffin <jimmy.chaffin@finnpartners.com>;',
							'Reply-To: Jimmy Chaffin <jimmy.chaffin@finnpartners.com>;'
						);
			$message = 'Please fill out the Creative Brief located here: ' . $link;			
		    wp_mail( $to, $subject, $message, $headers );
			
			// Save the fields to the post
			do_action( 'acf/save_post' , $post_id );
			
			// Update Brief ACF fields with inputs from Brief Sender
			update_field('field_5ba250aeba333', $client, $post_id); // Client
			update_field('field_5ba250efba334', $project_name, $post_id); // Project
			update_field('field_5babed871754f', $recip_name, $post_id); // Name
		
		} // foreach

		return $post_id;
	}
	
	// Assign Archive, Category, & Single Page Templates
	
	public function fp_brief_archive_template( $archive_template ) {
		global $post;
		if ( is_post_type_archive ( 'fp-brief' ) ) {
			$archive_template = dirname( __FILE__ ) . '/archive-brief.php';
		}
		return $archive_template;
	}
	
	public function fp_brief_single_template( $single_template ) {
		global $post;
		if ( $post->post_type == 'fp-brief' ) {
			$single_template = dirname( __FILE__ ) . '/single-brief.php';
		}
		return $single_template;
	}

	public function fp_brief_category_template( $category_template ) {
		global $post;
		if ( is_tax ( 'fp_brief_projects' ) ) {
			$category_template = dirname( __FILE__ ) . '/category-brief.php';
		}
		return $category_template;
	}
	
	// Add Creative Brief Template
		
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}
	 
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	} 

	public function view_project_template( $template ) {
		
		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		} 

		$file = plugin_dir_path( __FILE__ ). get_post_meta( 
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}
	
	// Register Sidebar
	
    public function fp_brief_sidebar() {
	    $text_domain = "fp-brief";
	    register_sidebar( array(
		    'id'          => 'fp-brief',
		    'name'        => __( 'Creative Brief Sidebar', $text_domain ),
		    'description' => __( 'This sidebar is located on the Creative Brief pages.', $text_domain ),
		) );
    }
				
	// Include Styles
	
	public function fp_brief_frontend_styles() {
		if (	get_post_type() == 'fp-brief'
				or is_post_type_archive('fp-brief')
				or is_tax('fp_brief_projects')
				or is_page_template('template-creative-brief-form.php')
				or is_page_template('template-creative-brief-sender.php')
			) {										
			$pluginpath = plugin_dir_url( __FILE__ );
			wp_enqueue_script( 'sidr_js', 'https://cdn.jsdelivr.net/jquery.sidr/2.2.1/jquery.sidr.min.js', array(), '2.2.1', true );
			wp_enqueue_script( 'js-topbutton', $pluginpath . 'js/topbutton.js' );
			wp_enqueue_style( 'fp-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css', false );			
			wp_enqueue_style( 'fp-google-fonts', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700', false );
			wp_enqueue_style( 'style', $pluginpath . 'css/style.css', 999 );			 
			add_action('wp_head', 'noindex', 1);
		}
	}

}

new FP_Brief_Plugin();