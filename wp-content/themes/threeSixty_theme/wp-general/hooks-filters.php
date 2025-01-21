<?php

//region wpautop remove filter
function acf_wysiwyg_remove_wpautop()
{
  remove_filter('acf_the_content', 'wpautop');
}

//endregion wpautop remove filter

// region Hide regular custom fields metabox
add_filter('acf/settings/remove_wp_meta_box', '__return_true');
// endregion Hide regular custom fields metabox

//region ACF image optimization
add_filter('max_srcset_image_width', 'awesome_acf_max_srcset_image_width', 10,
  2);
function awesome_acf_max_srcset_image_width()
{
  return 2200;
}

//endregion ACF image optimizatio

//region RethreeSixty_theme Paragraph Tags - ACF shortcode [check]
function my_acf_load_value($value, $post_id, $field)
{
  $content = apply_filters('the_content', $value);
  $content = force_balance_tags($content);
  $content = preg_replace('#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content);
  $content = preg_replace('~\s?<p>(\s| )+</p>\s?~', '', $content);

  return $content;
}

add_filter('acf/load_value/type=wysiwyg', 'my_acf_load_value', 10, 3);
//endregion RethreeSixty_theme Paragraph Tags - ACF shortcode [check]

//region Make a small-wysiwyg version - use small-field class
add_action('admin_head', 'admin_styles');
function admin_styles()
{
  ?>
  <style>
    .small-field .acf-editor-wrap iframe,
    .small-field .acf-editor-wrap.delay .wp-editor-area {
      min-height: 0 !important;
      height: 100px !important;
    }

    .medium-field .acf-editor-wrap iframe,
    .medium-field .acf-editor-wrap.delay .wp-editor-area {
      min-height: 0 !important;
      height: 200px !important;
    }

    .wp-block-freeform.block-library-rich-text__tinymce p {
      min-height: 2vh;
      margin: 0 !important;
    }
  </style>
  <?php
}

//endregion Make a small-wysiwyg version - use small-field class

//region THE SPEED OPTIMIZATION PARADISE
//region Remove emoji_icons from head
function disable_wp_emoji_icons()
{
  // all actions related to emojis
  remove_action('admin_print_styles', 'print_emoji_styles');
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action('wp_print_styles', 'print_emoji_styles');
  remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
  remove_filter('the_content_feed', 'wp_staticize_emoji');
  remove_filter('comment_text_rss', 'wp_staticize_emoji');
}

add_action('init', 'disable_wp_emoji_icons');
//endregion Remove emoji_icons from head
//region Remove [dashicons - admin-bar - duplicate-post - yoast-seo-adminbar - wp-block-library-theme - wp-block-library - wc-block-style ] CSS from loading on the frontend
function smartwp_remove_wp_block_library_css()
{
  if (!is_user_logged_in()) {
    wp_dequeue_style('dashicons');
    wp_dequeue_style('admin-bar');
    wp_dequeue_style('duplicate-post');
    wp_dequeue_style('yoast-seo-adminbar');
  }
//  wp_dequeue_style( 'wp-block-library' );
//  wp_dequeue_style( 'wp-block-library-theme' );
//  wp_dequeue_style( 'wc-block-style' );
}

add_action('wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100);
//endregion Remove [dashicons - admin-bar - duplicate-post - yoast-seo-adminbar - wp-block-library-theme - wp-block-library - wc-block-style ] CSS from loading on the frontend
//region Remove JQuery migrate
function remove_jquery_migrate($scripts)
{
  if (!is_admin() && isset($scripts->registered['jquery'])) {
    $script = $scripts->registered['jquery'];

    if ($script->deps) { // Check whether the script has any dependencies
      $script->deps = array_diff($script->deps, array(
        'jquery-migrate'
      ));
    }
  }
}

add_action('wp_default_scripts', 'remove_jquery_migrate');
//endregion Remove JQuery migrate
//region Eliminate render-blocking resources
if (!is_admin()) {
  $deferredScriptHandles = ['theme-main'];
  $deferredStyleHandles = [];

  add_filter('script_loader_tag', function ($tag, $handle) {
    global $deferredScriptHandles;
    if (!in_array($handle, $deferredScriptHandles)) {
      return $tag;
    }

    return str_replace(' src', ' defer="defer" src', $tag);
  }, 10, 2);
  add_filter('style_loader_tag', function ($tag, $handle) {
    global $deferredStyleHandles;
    if (!in_array($handle, $deferredStyleHandles)) {
      return $tag;
    }

    return str_replace(' rel',
        ' media="print" onload="this.onload=null;this.media=\'all\'" rel',
        $tag) . "<noscript>" . $tag . '</noscript>';
  }, 10, 2);


  if ($_SERVER['REQUEST_URI'] == '/') {
    function remove_quform()
    {
//      wp_dequeue_script('jquery-core');
      wp_deregister_script('quform');
      wp_deregister_style('quform');
    }

    add_action('wp_print_scripts', 'remove_quform');
  }
  /**
   * Enqueue the quform css and js if the quform  hortcode is being used
   */
  //  function quform_shortcode_scripts() {
  //    $pageid = get_the_id();
  //    $page_info = get_fields($pageid);
  //    if ( in_array( 'quform',$page_info) ) {
  //      echo 'a7aaaaaaaaaaa';
  //      wp_enqueue_script( 'quform');
  //      wp_enqueue_style( 'quform');
  //    }
  //  }
  //  add_action( 'wp_enqueue_scripts', 'quform_shortcode_scripts');
}
//endregion Eliminate render-blocking resources
//endregion THE SPEED OPTIMIZATION PARADISE

// region admin hook add no index meta in faq posts
function meta_robots_hook()
{
  global $post;
  if (@$post->post_type === 'faqs') {
    ?>
    <meta name="robots" content="noindex">
    <?php
  }
}

add_action('wp_head', 'meta_robots_hook');
// endregion admin hook add no index meta in faq posts

// region search redirect to home page if search query is empty
function search_redirect($query)
{
  if (!is_admin() && $query->is_main_query()) {
    if ($query->is_search && @$query->query['s'] === '') {
      wp_redirect(home_url());
      exit;
    }
  }
}

add_action('pre_get_posts', 'search_redirect');
// endregion search redirect to home page if search query is empty

//region custom pre get posts to handle paged with wp paginate

function custom_pre_get_posts($query)
{
  if ($query->is_main_query() && !$query->is_feed() && !is_admin() && (is_category() || is_tag() || is_tax())) {
    $query->set('page_val', get_query_var('paged'));
    $query->set('paged', 0);
  }
}

add_action('pre_get_posts', 'custom_pre_get_posts');

//endregion custom pre get posts to handle paged with wp paginate

/**
 * Filter the list of attachment image attributes.
 *
 * @param array $attrs Attributes for the image markup.
 * @param WP_Post $attachment Image attachment post.
 * @param string|array $size Requested size. Image size or array of width and height values
 *                                 (in that order). Default 'thumbnail'.
 *
 * @return array        $attrs        Attributes for the image markup.
 */
function default_attachment_alt($attrs, $attachment, $size)
{
  if (empty($attrs['alt'])) {
    $attrs['alt'] = $attachment?->post_title;
  }

  return $attrs;
}

add_filter('wp_get_attachment_image_attributes', 'default_attachment_alt', 10, 3);

// region remove WordPress version number as a <meta> tag

remove_action('wp_head', 'wp_generator');

// endregion remove WordPress version number as a <meta> tag


// save featured image dynamic after saving post
add_action('save_post', 'update_featured_image_after_saving_cpt', 10, 3);
function update_featured_image_after_saving_cpt($post_id, $post, $update)
{
  $post_types = ['post', 'articles', 'interviews', 'case_studies','books','teams'];
  if (!in_array($post->post_type, $post_types) || !$update) {
//    var_dump($post->post_type);
//    exit;
    return;
  }
  $featured_image = get_field('featured_image', $post_id) ?: "";
  update_post_meta($post_id, '_thumbnail_id', $featured_image);
}
