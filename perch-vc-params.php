<?php
/*
Plugin Name:  Visual composer param extension
Description:  Image upload, number field
Version:      1.0.0
Author:       ThemePerch
Author URI:   http://themeperch.net/
License:
Copyright (C) 2015 Jon Masterson
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define( 'PVCT_VERSION', '1.0' );
define( 'PVCT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( !is_plugin_active( 'js_composer/js_composer.php' ) ) :
  function pvct_module_modules_admin_notice__error() {
		$class = 'notice notice-error';
		$message = __( 'Oops! An error has occurred. Visual composer param extension plugin is disabled. This plugin only worked when WPBakery Visual Composer plugin is activated.', 'pvct' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	}
	add_action( 'admin_notices', 'pvct_module_modules_admin_notice__error' );


else:

/**
 * Register the stylesheets for the public-facing side of the site.
 * @since    1.0
 */
add_action( 'wp_enqueue_scripts', 'pvct_enqueue_scripts' );
function pvct_enqueue_scripts() {

	wp_enqueue_script( 'pvct-js', plugins_url( 'js/scripts.js', __FILE__), array( 'jquery' ), PVCT_VERSION, false );
	wp_localize_script( 'pvct-js', 'pvct', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'button_text' => __( 'Insert URL', 'pvct' ),
	) ); 
}

add_action( 'admin_enqueue_scripts', 'pvct_enqueue_scripts' );

if( !function_exists('pvct_get_posts_dropdown') ):
function pvct_get_posts_dropdown( $args = array() ) {
    global $wpdb, $post;

    $dropdown = array();
    $the_query = new WP_Query( $args );
    if ( $the_query->have_posts() ) {
        while ( $the_query->have_posts() ) {
            $the_query->the_post(); 
            $dropdown[get_the_ID()] = get_the_title();
        }
    }
    wp_reset_postdata();

    return $dropdown;
}
endif;

if( !function_exists('pvct_get_terms') ):
function pvct_get_terms( $tax = 'category', $key = 'id' ) {
    $terms = array();

    if(!taxonomy_exists($tax)) return false;

    if ( $key === 'id' ) foreach ( (array) get_terms( $tax, array( 'hide_empty' => false ) ) as $term ) $terms[$term->term_id] = $term->name;
      elseif ( $key === 'slug' ) foreach ( (array) get_terms( $tax, array( 'hide_empty' => false ) ) as $term ) $terms[$term->slug] = $term->name;
        return $terms;
}
endif;


if(!function_exists('pvct_vc_image_upload_settings_field')):
function pvct_vc_image_upload_settings_field($settings, $value){
  return '<div class="pvct-upload-container">
      <input type="text" name="' . esc_attr( $settings['param_name'] ) . '" value="'.esc_url($value).'" class="wpb_vc_param_value wpb-textinput perch-generator-attr perch-generator-upload-value" />
      <a href="javascript:;" class="button pvct-upload-button"><span class="wp-media-buttons-icon"></span>'.__( 'Media manager', 'pvct' ).'</a>
      <img width="80" src="'.esc_url($value).'" alt="">     
    </div>';
}
endif;

if(!function_exists('pvct_number_settings_field')):
function pvct_number_settings_field( $settings, $value ) {
   return '<div class="my_param_block">'
             .'<input name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
             esc_attr( $settings['param_name'] ) . ' ' .
             esc_attr( $settings['type'] ) . '_field" type="number" min="'.intval($settings['min']).'" max="'.intval($settings['max']).'" step="'.intval($settings['step']).'" value="' . esc_attr( $value ) . '" />' .
             '</div>'; // This is html markup that will be outputted in content elements edit form
}
endif;

if(!function_exists('pvct_perch_select_settings_field')):
function pvct_perch_select_settings_field( $args, $value ) {
    $selected = is_array($value)? $value : explode(',', $value);
    $args = wp_parse_args( $args, array(
        'param_name'       => '',
        'heading'     => '',
        'class'    => 'wpb_vc_param_value wpb-input wpb-select dropdown',
        'multiple' => '',
        'size'     => '',
        'disabled' => '',
        'selected' => $selected,
        'none'     => '',
        'value'  => array(),
        'style' => '',
        'format'   => 'keyval', // keyval/idtext
        'noselect' => '' // return options without <select> tag
      ) );
    $options = array();
    if ( !is_array( $args['value'] ) ) $args['value'] = array();
     if ( $args['param_name'] ) $name = ' name="' . $args['param_name'] . '"';
    if ( $args['param_name'] ) $args['param_name'] = ' id="' . $args['param_name'] . '"';   
    if ( $args['class'] ) $args['class'] = ' class="' . $args['class'] . '"';
    if ( $args['style'] ) $args['style'] = ' style="' . esc_attr( $args['style'] ) . '"';
    if ( $args['multiple'] ) $args['multiple'] = ' multiple="multiple"';
    if ( $args['disabled'] ) $args['disabled'] = ' disabled="disabled"';
    if ( $args['size'] ) $args['size'] = ' size="' . $args['size'] . '"';
    if ( $args['none'] && $args['format'] === 'keyval' ) $args['options'][0] = $args['none'];
    if ( $args['none'] && $args['format'] === 'idtext' ) array_unshift( $args['options'], array( 'id' => '0', 'text' => $args['none'] ) );
    
    // keyval loop
    // $args['options'] = array(
    //   id => text,
    //   id => text
    // );
    if ( $args['format'] === 'keyval' ) foreach ( $args['value'] as $id => $text ) {
        $options[] = '<option value="' . (string) $id . '">' . (string) $text . '</option>';
      }
    // idtext loop
    // $args['options'] = array(
    //   array( id => id, text => text ),
    //   array( id => id, text => text )
    // );
    elseif ( $args['format'] === 'idtext' ) foreach ( $args['options'] as $option ) {
        if ( isset( $option['id'] ) && isset( $option['text'] ) )
          $options[] = '<option value="' . (string) $option['id'] . '">' . (string) $option['text'] . '</option>';
      }
    $options = implode( '', $options );

    if(is_array($args['selected'])){
        foreach ($args['selected'] as $key => $value) {
          $options = str_replace( 'value="' . $value . '"', 'value="' . $value . '" selected="selected"', $options );
        }
    }else{
      $options = str_replace( 'value="' . $args['selected'] . '"', 'value="' . $args['selected'] . '" selected="selected"', $options );
    }
    
    $output = ( $args['noselect'] ) ? $options : '<select' .$name. $args['param_name'] . $args['class'] . $args['multiple'] . $args['size'] . $args['disabled'] . $args['style'] . '>' . $options . '</select>';
   // $output .= '<input type="hidden" '.$name.' value="'.$value.'">';
    return '<div class="perch_select_param_block">'.$output.'</div>';
}
endif;

if(function_exists('vc_add_shortcode_param')){ 
    vc_add_shortcode_param( 'pvct_number', 'pvct_number_settings_field' );
    vc_add_shortcode_param( 'pvct_select', 'pvct_perch_select_settings_field' );
    vc_add_shortcode_param( 'pvct_image_upload', 'pvct_vc_image_upload_settings_field' );   
}

  
endif;

include_once PVCT_PLUGIN_DIR.'/example.php';