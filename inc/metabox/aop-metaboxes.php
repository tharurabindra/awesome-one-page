<?php
/**
 * This function is responsible for rendering metaboxes in single post/page area
 * 
 * @package Awesome_One_Page
 */
 
 add_action( 'add_meta_boxes', 'aop_add_layout_metabox' );
/**
 * Add Meta Boxes.
 */
function aop_add_layout_metabox() {
	// Adding layout meta box for Page
	add_meta_box( 'page-layout', esc_html__( 'Select Layout', 'awesome-one-page' ), 'aop_layout_call', 'page', 'normal', 'high' );
	// Adding layout meta box for Post
	add_meta_box( 'page-layout', esc_html__( 'Select Layout', 'awesome-one-page' ), 'aop_layout_call', 'post', 'normal', 'high' );
}

global $aop_page_specific_layout;
$aop_page_specific_layout = array(
	'default-layout' 	=> array(
		'id'			=> 'aop_page_specific_layout',
		'value' 		=> 'default_layout',
		'label' 		=> esc_html__( 'Default', 'pageline' ),
		'thumbnail' 	=> get_template_directory_uri() . '/inc/assets/images/default-sidebar.png'
	),
	'right-sidebar' 	=> array(
		'id'			=> 'aop_page_specific_layout',
		'value' 		=> 'right_sidebar',
		'label' 		=> esc_html__( 'Right Sidebar', 'awesome-one-page' ),
		'thumbnail' 	=> get_template_directory_uri() . '/inc/assets/images/right-sidebar.png'
	),
	'left-sidebar' 		=> array(
		'id'			=> 'aop_page_specific_layout',
		'value' 		=> 'left_sidebar',
		'label' 		=> esc_html__( 'Left Sidebar', 'awesome-one-page' ),
		'thumbnail' 	=> get_template_directory_uri() . '/inc/assets/images/left-sidebar.png'
	),
	'no-sidebar-full-width' => array(
		'id'			=> 'aop_page_specific_layout',
		'value' 		=> 'no_sidebar_full_width',
		'label' 		=> esc_html__( 'No Sidebar Full Width', 'awesome-one-page' ),
		'thumbnail' 	=> get_template_directory_uri() . '/inc/assets/images/no-sidebar-full-width-layout.png'
	),
	'no-sidebar-content-centered' => array(
		'id'			=> 'aop_page_specific_layout',
		'value' 		=> 'no_sidebar_content_centered',
		'label' 		=> esc_html__( 'No Sidebar Content Centered', 'awesome-one-page' ),
		'thumbnail' 	=> get_template_directory_uri() . '/inc/assets/images/no-sidebar-content-centered-layout.png'
	)
);

function aop_layout_call() {
	global $aop_page_specific_layout;
	aop_layout_meta_form( $aop_page_specific_layout );
}

/**
 * Displays metabox to for select layout option
 */
function aop_layout_meta_form( $aop_layout_metabox_field ) {
	global $post;

	// Use nonce for verification
	wp_nonce_field( basename( __FILE__ ), 'aop_layout_metabox_nonce' ); ?>

	<table class="form-table">
		<tr>
			<td colspan="4"><em class="f13">Choose Sidebar Template</em></td>
		</tr>
		<tr>
			<td>
			<?php 
				$img_count = 0 ;
				foreach ( $aop_layout_metabox_field as $field ) {
					$img_count++;
					$layout_meta = get_post_meta( $post->ID, $field['id'], true );
					$default_class ='';
					switch( $field['id'] ) {

						// Layout
						case 'aop_page_specific_layout':
							if( empty( $layout_meta ) && $img_count == '1' ) { 
								$layout_meta = 'default_layout'; $default_class = 'aop-radio-image-selected'; 
							}
							if ( $field['value'] == $layout_meta ) { $default_class = 'aop-radio-image-selected'; }?>

							<div class="aop-radio-image-wrapper" style="float:left; margin-right:30px;">
				                <label class="aop-description">
					                <img class="aop-radio-image <?php echo esc_attr( $default_class ); ?>" src="<?php echo esc_url( $field['thumbnail'] ); ?>" alt="<?php echo esc_attr( $field['label'] );?>" title="<?php echo esc_attr( $field['label'] );?>" />
					                <input style = "display:none" type="radio" name="<?php echo esc_attr($field['id']); ?>" value="<?php echo esc_attr($field['value']); ?>" <?php checked( $field['value'], $layout_meta ); ?>/>
				                </label>
			                </div>
							<?php

						break;
					}
				} 
			?>
			</td>
		</tr>
	</table>
<?php 
}

add_action('save_post', 'aop_save_layout_metabox');
/**
 * save the custom metabox data
 * @hooked to save_post hook
 */
function aop_save_layout_metabox( $post_id ) {
	global $aop_page_specific_layout, $post;

	// Verify the nonce before proceeding.
   if ( !isset( $_POST[ 'aop_layout_metabox_nonce' ] ) || !wp_verify_nonce( $_POST[ 'aop_layout_metabox_nonce' ], basename( __FILE__ ) ) )
		return;

	// Stop WP from clearing custom fields on autosave
   if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
		return;

	if ('page' == $_POST['post_type']) {
		if (!current_user_can( 'edit_page', $post_id ) )
			return $post_id;
	}
	elseif (!current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	foreach ( $aop_page_specific_layout as $field ) {
		//Execute this saving function
		$old = get_post_meta( $post_id, $field['id'], true);
		$new = sanitize_key( $_POST[$field['id']] );
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	} // end foreach
}

/****************************************************************************************/

add_action( 'add_meta_boxes', 'aop_add_custom_metabox' );
/**
 * Add Meta Boxes.
 */
function aop_add_custom_metabox() {
	//Adding fontawesome icons
	add_meta_box( 'services-icon', esc_html__( 'Icon Class', 'awesome-one-page' ), 'aop_service_icon_call', 'page', 'side' );
	//Adding Team designation meta box
	add_meta_box( 'team-designation', esc_html__( 'Designation', 'awesome-one-page' ), 'aop_team_designation_call', 'page', 'side' );
	//Adding Team Social Links meta box
	add_meta_box( 'team-social', esc_html__( 'Social Links', 'awesome-one-page' ), 'aop_team_social_call', 'page', 'side' );
	//Adding Testimonial designation meta box
	add_meta_box( 'testimonial-designation', esc_html__( 'Designation', 'awesome-one-page' ), 'aop_testimonial_designation_call', 'page', 'side' );
}

global $aop_metabox_field_service_icons, $aop_metabox_field_team_desigmation, $aop_metabox_field_team_social, $aop_metabox_field_testimonial_designation;

$aop_metabox_field_service_icons = array(
	array(
		'id'			=> 'aop_service_font_icon',
		'label' 		=> esc_html__( 'If featured image is not used than display the icon in Services.', 'awesome-one-page' )
	)
);

$aop_metabox_field_team_desigmation = array(
	array(
		'id'			=> 'aop_team_designation',
		'label' 		=> esc_html__( 'Show designation in Team Widget.', 'awesome-one-page' )
	)
);

$aop_metabox_field_team_social = array(
	array(
		'id'			=> 'aop_team_social_1',
		'label' 		=> esc_html__( 'Social Link One:', 'awesome-one-page' )
	),
	array(
		'id'			=> 'aop_team_social_2',
		'label' 		=> esc_html__( 'Social Link Two:', 'awesome-one-page' )
	),
	array(
		'id'			=> 'aop_team_social_3',
		'label' 		=> esc_html__( 'Social Link Three:', 'awesome-one-page' )
	)
);

$aop_metabox_field_testimonial_designation = array(
	array(
		'id'			=> 'aop_testimonial_designation',
		'label' 		=> esc_html__( 'Testimonial Designation', 'awesome-one-page' )
	)
);

function aop_service_icon_call() {
	global $aop_metabox_field_service_icons;
	aop_metabox_form( $aop_metabox_field_service_icons );
}

function aop_team_designation_call() {
	global $aop_metabox_field_team_desigmation;
	aop_metabox_form( $aop_metabox_field_team_desigmation );
}

function aop_team_social_call() {
	global $aop_metabox_field_team_social;
	aop_metabox_form( $aop_metabox_field_team_social );
}

function aop_testimonial_designation_call() {
	global $aop_metabox_field_testimonial_designation;
	aop_metabox_form( $aop_metabox_field_testimonial_designation );
}

/**
 * Displays metabox to for select layout option
 */
function aop_metabox_form( $aop_metabox_fields ) {
	global $post;

	// Use nonce for verification
	wp_nonce_field( basename( __FILE__ ), 'custom_metabox_nonce' );

	foreach ( $aop_metabox_fields as $field ) {
		$layout_meta = get_post_meta( $post->ID, $field['id'], true );
		switch( $field['id'] ) {

			// Font icon
			case 'aop_service_font_icon': ?>
			<div class="aop-metabox-input-wrap">
	        	<label><?php esc_html_e( $field['label'] ); ?></label>
	          	<input type="text" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $layout_meta ); ?>"></br>
	          	<?php 
				$url = 'http://fontawesome.io/icons/';
				$link = sprintf( __( '<a href="%s" target="_blank">Refer here</a> for icon class. For example: <strong>fa-mobile</strong>', 'awesome-one-page' ), esc_url( $url ) );
				echo '<span class="aop-metabox-info">'.$link.'</span>'; ?>
	        </div><!-- .aop-metabox-input-wrap -->
			<?php break;

			// Team Designation
			case 'aop_team_designation': ?>
			<div class="aop-metabox-input-wrap">
	        	<label><?php esc_html_e( $field['label'] ); ?></label>
	          	<input type="text" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $layout_meta ); ?>"></br>
	        </div><!-- .aop-metabox-input-wrap -->
			<?php break;

			// Team Social Links One
			case 'aop_team_social_1': ?>
			<div class="aop-metabox-input-wrap">
	        	<label><?php esc_html_e( $field['label'] ); ?></label>
	          	<input type="text" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $layout_meta ); ?>"></br>
	        </div><!-- .aop-metabox-input-wrap -->
			<?php break;

			// Team Social Links Two
			case 'aop_team_social_2': ?>
			<div class="aop-metabox-input-wrap">
	        	<label><?php esc_html_e( $field['label'] ); ?></label>
	          	<input type="text" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $layout_meta ); ?>"></br>
	        </div><!-- .aop-metabox-input-wrap -->
			<?php break;

			// Team Social Links Three
			case 'aop_team_social_3': ?>
			<div class="aop-metabox-input-wrap">
	        	<label><?php esc_html_e( $field['label'] ); ?></label>
	          	<input type="text" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $layout_meta ); ?>"></br>
	        </div><!-- .aop-metabox-input-wrap -->
			<?php break;

			// Testimonial Designation
			case 'aop_testimonial_designation': ?>
			<div class="aop-metabox-input-wrap">
	        	<label><?php esc_html_e( $field['label'] ); ?></label>
	          	<input type="text" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $layout_meta ); ?>"></br>
	        </div><!-- .aop-metabox-input-wrap -->
			<?php break;
		}
	}
}

add_action('save_post', 'aop_save_custom_meta');
/**
 * save the custom metabox data
 * @hooked to save_post hook
 */
function aop_save_custom_meta( $post_id ) {
	global $aop_metabox_field_service_icons, $aop_metabox_field_team_desigmation, $aop_metabox_field_team_social, $aop_metabox_field_testimonial_designation, $post;

	// Verify the nonce before proceeding.
   if ( !isset( $_POST[ 'custom_metabox_nonce' ] ) || !wp_verify_nonce( $_POST[ 'custom_metabox_nonce' ], basename( __FILE__ ) ) )
      return;

	// Stop WP from clearing custom fields on autosave
   if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
      return;

	if ('page' == $_POST['post_type']) {
      if (!current_user_can( 'edit_page', $post_id ) )
         return $post_id;
   }
   elseif (!current_user_can( 'edit_post', $post_id ) ) {
      return $post_id;
   }

	if ('page' == $_POST['post_type']) {
   	// loop through fields and save the data- Service widget
	   foreach ( $aop_metabox_field_service_icons as $field ) {
	    	$old = get_post_meta( $post_id, $field['id'], true );
	      $new = $_POST[$field['id']];
	      if ($new && $new != $old) {
	     		update_post_meta( $post_id,$field['id'],$new );
	      } elseif ('' == $new && $old) {
	     	delete_post_meta($post_id, $field['id'], $old);
	    	}
	   } // end foreach

	   // loop through fields and save the data- Team widget
	   foreach ( $aop_metabox_field_team_desigmation as $field ) {
	    	$old = get_post_meta( $post_id, $field['id'], true );
	      $new = $_POST[$field['id']];
	      if ($new && $new != $old) {
	     		update_post_meta( $post_id,$field['id'],$new );
	      } elseif ('' == $new && $old) {
	     	delete_post_meta($post_id, $field['id'], $old);
	    	}
	   } // end foreach

	   // loop through fields and save the data- Team widget
	   foreach ( $aop_metabox_field_team_social as $field ) {
	    	$old = get_post_meta( $post_id, $field['id'], true );
	      $new = $_POST[$field['id']];
	      if ($new && $new != $old) {
	     		update_post_meta( $post_id,$field['id'],$new );
	      } elseif ('' == $new && $old) {
	     	delete_post_meta($post_id, $field['id'], $old);
	    	}
	   } // end foreach

	   // loop through fields and save the data- Testimonial widget
	   foreach ( $aop_metabox_field_testimonial_designation as $field ) {
	    	$old = get_post_meta( $post_id, $field['id'], true );
	      $new = $_POST[$field['id']];
	      if ($new && $new != $old) {
	     		update_post_meta( $post_id,$field['id'],$new );
	      } elseif ('' == $new && $old) {
	     	delete_post_meta($post_id, $field['id'], $old);
	    	}
	   } // end foreach
	}
}
