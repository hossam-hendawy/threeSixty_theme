<?php

/**
 * Digihive Theme file includes and definitions
 *
 * @package digihive
 */

// region CONSTANTS

if (!defined('DH_IMAGE_DIR')) {
  define('DH_IMAGE_DIR', untrailingslashit(get_template_directory_uri()) . '/images/');
}

// endregion CONSTANTS

// region  Detect if WP_DEBUG is not enabled

//if ($_SERVER['HTTP_HOST'] === 'localhost' && defined('WP_DEBUG') && WP_DEBUG === false) {
//  echo "<h1 style='color:red;'>Please Don't Disable WP_DEBUG Mood</h1>";
//  echo "<ol style='color:blue; font-size: 20px;'>
//          <li>Go to root folder</li>
//          <li>Search for wp-config file and then open it</li>
//          <li>Scroll down until you find define( 'WP_DEBUG', false );</li>
//          <li>change value of WP_DEBUG to true</li>
//        </ol>";
//  echo "<h2 style='color:blue;'>Thanks</h2>";
//  exit;
//}

// endregion  Detect if WP_DEBUG is not enabled

// region Check php version
if ($_SERVER['HTTP_HOST'] === 'localhost' && phpversion() < 8) {
  echo "<h1 style='color:red;'>Please update php version to 8.0.1</h1>";
  exit;
}
// endregion Check php version

// region detect if there an error in the site remove opacity from the body

if ($_SERVER['HTTP_HOST'] === 'localhost' && !str_contains($_SERVER['REQUEST_URI'], 'wp-admin') && !str_contains($_SERVER['REQUEST_URI'], 'wp-json')) {
  add_action('wp_error_added', 'custom_wp_error_added_action', 10, 4);
}
/**
 * Function for `wp_error_added` action-hook.
 *
 * @param string|int $code Error code.
 * @param string $message Error message.
 * @param mixed $data Error data. Might be empty.
 * @param WP_Error $wp_error The WP_Error object.
 *
 * @return void
 */
function custom_wp_error_added_action($code, $message, $data, $wp_error)
{
  echo "<style>body{opacity: 1 !important;}</style>";
  // action...
}

// endregion detect if there an error in the site remove opacity from the body

//region Sets up theme
/**
 * swight_theme functions and definitions
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package swight_theme
 */

if (!defined('_S_VERSION')) {
  // Replace the version number of the theme on each release.
  define('_S_VERSION', '1.0.0');
}

if (!function_exists('swight_theme_setup')) :
  /**
   * Sets up theme defaults and registers support for various WordPress features.
   *
   * Note that this function is hooked into the after_setup_theme hook, which
   * runs before the init hook. The init hook is too late for some features, such
   * as indicating support for post thumbnails.
   */
  function swight_theme_setup()
  {
    /*
 * Make theme available for translation.
 * Translations can be filed in the /languages/ directory.
 * If you're building a theme based on swight_theme, use a find and replace
 * to change 'swight_theme' to the name of your theme in all the template files.
 */
    load_theme_textdomain('swight_theme', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
 * Let WordPress manage the document title.
 * By adding theme support, we declare that this theme does not use a
 * hard-coded <title> tag in the document head, and expect WordPress to
 * provide it for us.
 */
    add_theme_support('title-tag');

    /*
 * Enable support for Post Thumbnails on posts and pages.
 *
 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
 */
    add_theme_support('post-thumbnails');

    /**
     * Some blocks in Gutenberg like tables, quotes, separator benefit from structural styles (margin, padding, border etc…)
     * They are applied visually only in the editor (back-end) but not on the front-end to avoid the risk of conflicts with the styles wanted in the theme.
     * If you want to display them on front to have a base to work with, in this case, you can add support for wp-block-styles, as done below.
     * @see  Theme Styles.
     * @link https://make.wordpress.org/core/2018/06/05/whats-new-in-gutenberg-5th-june/, https://developer.wordpress.org/block-editor/developers/themes/theme-support/#default-block-styles
     */
    add_theme_support('wp-block-styles');

    /**
     * Loads the editor styles in the Gutenberg editor.
     *
     * Editor Styles allow you to provide the CSS used by WordPress’ Visual Editor so that it can match the frontend styling.
     * If we don't add this, the editor styles will only load in the classic editor ( tiny mice )
     *
     * @see https://developer.wordpress.org/block-editor/developers/themes/theme-support/#editor-styles
     */
    add_theme_support('editor-styles');

    /*
 * Switch default core markup for search form, comment form, and comments
 * to output valid HTML5.
 */
    add_theme_support('html5', array(
      'gallery',
      'caption',
    ));
  }
endif;
add_action('after_setup_theme', 'swight_theme_setup');
//endregion Sets up theme

//region Call theme helpers

require_once 'helpers/helpers.php';

//endregion Call theme helpers


//region register blocks

include 'blocks/blocks-related-functions.php';

//endregion register blocks

// region Enqueue scripts and styles.

include 'wp-general/enqueue-scripts-styles.php';

// endregion Enqueue scripts and styles.

// region wp general

include 'wp-general/hooks-filters.php';

include 'wp-general/ajax.php';

include 'wp-general/custom-image-sizes.php';

include 'wp-general/short-codes.php';

include 'wp-general/global-variables.php';

// endregion wp general


function threeSixty_theme_breadcrumbs()
{
  // Start the breadcrumb with a link to the home page
  echo '<nav class="site-breadcrumb">';

  // 🏠 الكلمة "Home" أو "الرئيسية"
  $home_label = apply_filters('wpml_translate_single_string', 'Home', 'theme-breadcrumbs', 'Home Label');
  echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html($home_label) . '</a>';

  // Add the separator
  echo ' / ';

  if (is_singular('post')) {
    // 📄 الكلمة "Blog"
    $blog_label = apply_filters('wpml_translate_single_string', 'Blog', 'theme-breadcrumbs', 'Blog Label');
    echo '<a href="' . site_url('blog') . '">' . esc_html($blog_label) . '</a>';
    echo ' / ';
  }

  if (is_singular()) {
    echo '<span>' . esc_html(get_the_title()) . '</span>';
  } elseif (is_category() || is_tag() || is_tax()) {
    echo '<span>' . esc_html(single_term_title('', false)) . '</span>';
  } elseif (is_post_type_archive()) {
    echo '<span>' . esc_html(post_type_archive_title('', false)) . '</span>';
  } elseif (is_search()) {
    // 🔍 "Search Results for :"
    $search_label = apply_filters('wpml_translate_single_string', 'Search Results for : ', 'theme-breadcrumbs', 'Search Label');
    echo '<span>' . esc_html($search_label) . esc_html(get_search_query()) . '</span>';
  } elseif (is_404()) {
    // ❌ "404 Not Found"
    $not_found_label = apply_filters('wpml_translate_single_string', '404 Not Found', 'theme-breadcrumbs', '404 Label');
    echo '<span>' . esc_html($not_found_label) . '</span>';
  }

  echo '</nav>';
}

// region plugins adjustments

include 'plugins-adjustments/gravity-from.php';

//include 'plugins-adjustments/tinymce/tinymce.php';

//include 'plugins-adjustments/tinymce/cta-button-addon/tinymce-extension.php';

include 'plugins-adjustments/acf.php';

// endregion plugins adjustments

// region custom acf fields

include 'custom-acf-fields/acf-table-field/acf-table.php';

// endregion custom acf fields

//include_once __DIR__ . '/acf-my-new-field/init.php';


function t($text, $context = 'theme-breadcrumbs', $name = '') {
  return apply_filters('wpml_translate_single_string', $text, $context, $name ?: $text);
}


function register_breadcrumb_strings_for_wpml() {
  do_action('wpml_register_single_string', 'theme-breadcrumbs', 'Home Label', 'Home');
  do_action('wpml_register_single_string', 'theme-breadcrumbs', 'Blog Label', 'Blog');
  do_action('wpml_register_single_string', 'theme-breadcrumbs', 'Search Label', 'Search Results for : ');
  do_action('wpml_register_single_string', 'theme-breadcrumbs', '404 Label', '404 Not Found');
  do_action('wpml_register_single_string', 'text', 'Explore More Label', 'Explore More');
  do_action('wpml_register_single_string', 'text', 'This package includes: Label', 'This package includes:');
  do_action('wpml_register_single_string', 'text', 'This package includes: Label', 'This package includes:');
  do_action('wpml_register_single_string', 'text', 'Contact Label', 'Contact');
  do_action('wpml_register_single_string', 'text', 'Help & Support Label', 'Help & Support');
  do_action('wpml_register_single_string', 'text', 'Back Label', 'Back');



}
add_action('init', 'register_breadcrumb_strings_for_wpml');
