<?php
// check if theme.json is being used and if so, grab the settings
if ( class_exists( 'WP_Theme_JSON_Resolver' ) ) {
  $settings = WP_Theme_JSON_Resolver::get_theme_data()->get_settings();
}

global $theme_color_pallets;
if ( isset( $settings ) && ! empty( $settings['color']['palette']['theme'] ) ) {
  $theme_color_pallets = array();
  foreach ( $settings['color']['palette']['theme'] as $color ) {
    $theme_color_pallets[ $color['color'] ] = "--wp--preset--color--" . $color["slug"];
  }
}


global $base_size;
$base_size = 10;
