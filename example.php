<?php 
/**
 * The VC Functions
 */
add_action( 'vc_before_init', 'pvct_extension_field_shortcode_vc');
function pvct_extension_field_shortcode_vc() {
	
	vc_map( 
		array(
			'icon' => 'pvct-icon',
			'name' => __('Example field', 'pvct'),
			'base' => 'pvct_example_field',
			'class' => 'pvct-vc',
			'category' => __('PVCT', 'pvct'),
			'description' => 'Display param extension for shortcode',			
			'params' => array(	
				array(
	                'type' => 'pvct_image_upload',
	                'value' => '',
	                'heading' => 'Image upload',
	                'param_name' => 'image',
	                'admin_label' => true
	            ),			
				array(
					'type' => 'pvct_select',
					'heading' => __('Perch select', 'pvct'),
					'param_name' => 'pvct_select',
					'value' =>  array(
								'h1' => __( 'H1', 'pvct' ),
								'h2' => __( 'H2', 'pvct' ),
								'h3' => __( 'H3', 'pvct' ),
								'h4' => __( 'H4', 'pvct' ),
								'h5' => __( 'H5', 'pvct' ),
								'h6' => __( 'H6', 'pvct' ),
							),
					'admin_label' => true
				),
				array(
					'type' => 'pvct_number',
					'heading' => __('Number', 'pvct'),
					'param_name' => 'number',
					'value' => 0,
					'min' => -1,
					'max' => '100',
					'step' => '1',
					'description' => '',
					'admin_label' => true
				),
				array(
					'type' => 'pvct_select',
					'multiple' => 'multiple',
					'heading' => __('Multiple Select category', 'pvct'),
					'param_name' => 'multiple_select_tax_term',
					'value' =>  pvct_get_terms(),
				),
				array(
					'type' => 'pvct_select',
					'heading' => __('Single Select category', 'pvct'),
					'param_name' => 'single_select_tax_term',
					'value' =>  pvct_get_terms(),
				),
				array(
                    'type' => 'pvct_select',
                    'value' => pvct_get_posts_dropdown(array('post_type' => 'post', 'posts_per_page' => -1)),
                    'heading' => 'Post select',
                    'param_name' => 'post_select',
                    'admin_label' => true,
                ),

			),	

	));
	
}