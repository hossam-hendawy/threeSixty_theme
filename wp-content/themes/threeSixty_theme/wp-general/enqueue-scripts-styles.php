<?php

add_action('wp_enqueue_scripts', 'threeSixty_theme_scripts');
function threeSixty_theme_scripts()
{

  if (file_exists(get_template_directory() . '/assets/manifest.json')) {
    $asset_map = json_decode(
      file_get_contents(get_template_directory() . '/assets/manifest.json'),
      true
    );
    $asset_map_js = array_filter($asset_map, function ($path) {
      return pathinfo($path)['extension'] == 'js';
    });
    foreach ($asset_map_js as $key => $path) {
      wp_enqueue_script($key, $path, [], ['jquery'], true);
    }

    $asset_map_css = array_filter($asset_map, function ($path) {
      return pathinfo($path)['extension'] == 'css';
    });
    foreach ($asset_map_css as $key => $path) {
      wp_enqueue_style($key, $path, [], null);
    }

  } else {
    wp_enqueue_script('main.js', get_template_directory_uri() . '/assets/main.js', ['jquery'], '1.0.0');
    wp_enqueue_style('main.css', get_template_directory_uri() . '/assets/main.css', null, '1.0.0');
  }

  wp_localize_script('main.js', 'theme_ajax_object',
    array(
      'ajax_url' => admin_url('admin-ajax.php'),
      '_ajax_nonce' => wp_create_nonce('nonce_ajax_more_posts'),
    )
  );
}

function load_jquery_in_header() {
  wp_enqueue_script('jquery');
}
add_action( 'wp_enqueue_scripts', 'load_jquery_in_header' );


//region ACF.js - Load Custom Gutenberg Style
function add_admin_style_to_post_page_only()
{

  wp_enqueue_script('admin', get_template_directory_uri() . '/assets/admin.js', null, rand(0, 100));

  wp_enqueue_style('admin', get_template_directory_uri() . '/assets/admin.css', null, rand(0, 100));

  wp_localize_script('admin', 'siteurl',
    array(
      'siteurl' => site_url(),
    )
  );

  // region enqueue blocks styles in wp admin

  //fixme check how to import blocks style in preview without affecting acf style

  function load_blocks_style_admin()
  {
    $glob = glob(get_template_directory() . '/assets/*.css');
    $index = 1;
    foreach ($glob as $element) {
      $index++;
      $elementx = get_template_directory_uri() . '/assets/' . explode('assets', $element)[1];
      wp_register_style("preview-style-$index", $elementx);
      wp_enqueue_style("preview-style-$index");
    }
  }

  // endregion enqueue blocks styles in wp admin
}

add_action('admin_head', 'add_admin_style_to_post_page_only');
//endregion ACF.js - Load Custom Gutenberg Style
