<?php

if ( ! defined( 'CTA_BUTTON_ADDON' ) ) {
  define( 'CTA_BUTTON_ADDON', untrailingslashit( get_template_directory_uri() ) . '/plugins-adjustments/tinymce/cta-button-addon' );
  define( 'CTA_BUTTONS_TEMPLATES', get_template_directory() . '/blocks/theme_buttons/button-templates' );
}

add_action( 'init', 'swp_buttons' );
/********* TinyMCE Buttons ***********/
if ( ! function_exists( 'swp_buttons' ) ) {
  function swp_buttons() {
    if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
      return;
    }
    if ( get_user_option( 'rich_editing' ) !== 'true' ) {
      return;
    }
    add_filter( 'mce_external_plugins', 'swp_add_buttons' );
    add_filter( 'mce_buttons', 'swp_register_buttons' );
  }
}

if ( ! function_exists( 'swp_add_buttons' ) ) {
  function swp_add_buttons( $plugin_array ) {
    $plugin_array['swpbtn'] = CTA_BUTTON_ADDON . '/tinymce_buttons.js';

    return $plugin_array;
  }
}
if ( ! function_exists( 'swp_register_buttons' ) ) {
  function swp_register_buttons( $buttons ) {
    array_push( $buttons, 'swpbtn' );

    return $buttons;
  }
}

function cod_cta( $atts, $content ) {
  //set shortcode attributes
  $custom_atts = shortcode_atts( array(
    'url'           => '',
    'text'          => '',
    'target'        => '',
    'type'          => '',
    'margin_top'    => '',
    'margin_bottom' => '',
  ), $atts );

  $url    = $custom_atts['url'];
  $text   = $custom_atts['text'];
  $target = $custom_atts['target'] ? '_blank' : '_self';
  $type   = $custom_atts['type'];
  $cta_mt = $custom_atts['margin_top'];
  $cta_mb = $custom_atts['margin_bottom'];

  ob_start();

  if ( file_exists( CTA_BUTTONS_TEMPLATES . "/$type.php" ) ) {

    include( CTA_BUTTONS_TEMPLATES . "/$type.php" );

  }

  return ob_get_clean();

}

if ( ! is_admin() ) {
  add_shortcode( 'cod_cta', 'cod_cta' );
}
