<?php

//region Add theme colors to acf color picker field

function add_theme_colors_to_acf_color_picker()
{
  global $theme_color_pallets;
  $theme_color_pallets = is_array($theme_color_pallets) && !empty($theme_color_pallets) ? array_keys($theme_color_pallets) : array();
  ?>
  <script type="text/javascript">
    (function ($) {
      const color_palettes = <?= json_encode($theme_color_pallets); ?>;

      if (color_palettes?.length) {
        acf.add_filter('color_picker_args', function (args, $field) {

          // do something to args
          args.palettes = color_palettes;

          // return
          return args;
        });
      }

    })(jQuery);
  </script>
  <?php
}

add_action('acf/input/admin_footer', 'add_theme_colors_to_acf_color_picker');

//endregion Add theme colors to acf color picker field

//region acf field to pull all custom image sizes
add_filter('acf/load_field/name=image_size', 'acf_load_all_custom_image_sizes');

function acf_load_all_custom_image_sizes($field)
{

  foreach (\Theme\Helpers::get_all_image_sizes() as $key => $size) {
    $field['choices'][$key] = $size['width'] . 'x' . $size['height'];
  }

  // return the field
  return $field;
}

//endregion acf field to pull all custom image sizes

// region auto redirect to acf sync page is there a sync fields

function acf_sync_notice()
{
  if (isset($_GET['post_type']) && $_GET['post_type'] == 'acf-field-group') {
    $class = 'notice notice-error';
    $message = __('PLEASE SYNC ACF FIELDS FIRST!', 'sample-text-domain');

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
  }
}

add_action('admin_init', 'auto_redirect_to_acf_sync');
function auto_redirect_to_acf_sync($views)
{
  $should = array();
  $obj = acf_get_local_json_files();

  if ($obj) {
    // Get all groups in a single cached query to check if sync is available.
    $all_field_groups = acf_get_field_groups();
    foreach ($all_field_groups as $field_group) {

      // Extract vars.
      $local = acf_maybe_get($field_group, 'local');
      $modified = acf_maybe_get($field_group, 'modified');
      $private = acf_maybe_get($field_group, 'private');

      // Ignore if is private.
      if ($private) {
        continue;

        // Ignore not local "json".
      } elseif ($local !== 'json') {
        continue;

        // Append to sync if not yet in database.
      } elseif (!$field_group['ID']) {
        $should[$field_group['key']] = $field_group;

        // Append to sync if "json" modified time is newer than database.
      } elseif ($modified && $modified > get_post_modified_time('U', true, $field_group['ID'])) {
        $should[$field_group['key']] = $field_group;
      }
    }
  }

  if (count($should) > 0) {
    add_action('admin_notices', 'acf_sync_notice');
  }
  if (!isset($_GET['post_status'])) {
    $post_status = '';
  } else {
    $post_status = $_GET['post_status'];
  }


//  $post_type == 'acf-field-group'
  if (count($should) > 0 && !in_array($post_status, array(
      'sync',
      'trash'
    )) && $_SERVER['HTTP_HOST'] === 'localhost' && $_POST == null) {

    ?>
    <script>
      window.location = '<?= admin_url() . '/edit.php?post_type=acf-field-group&post_status=sync'; ?>';
    </script>
    <?php
    //exit;
  }

  return $views;
}

// endregion auto redirect to acf sync page is there a sync fields

//region ACF show Options & Settings in Dashboard

// (Optional) Hide the ACF admin menu item.
add_filter('acf/settings/show_admin', 'my_acf_settings_show_admin');
function my_acf_settings_show_admin($show_admin)
{
  return true;
}

if (function_exists('acf_add_options_page')) {
  acf_add_options_page();
}

//endregion ACF show Options & Settings in Dashboard

// region hide acf panel and show it with query first time only -> show_acf_panel
if (!str_contains($_SERVER['HTTP_HOST'], 'localhost')):

  add_filter('acf/settings/show_admin', 'hide_acf_panel');

  function hide_acf_panel()
  {
    if (isset($_COOKIE['show_acf_panel'])) {
      return true;
    }
    return false;
  }

  add_action('admin_init', 'save_admin_cookie');

// save cookie show_acf_panel to show acf panel automatically
  function save_admin_cookie()
  {
    if (isset($_GET['show_acf_panel']) && !isset($_COOKIE['show_acf_panel'])) {
      setcookie('show_acf_panel', '1', time() + (60 * 60 * 24 * 365 * 100), '/');
    }
  }

endif;
//endregion
