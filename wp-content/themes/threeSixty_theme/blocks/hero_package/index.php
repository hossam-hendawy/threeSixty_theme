<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'hero_package';
if (isset($block)) {
  $id = 'block_' . uniqid();
  if (!empty($block['anchor'])) {
    $id = $block['anchor'];
  }

// Create class attribute allowing for custom "className" and "align" values.
  if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
  }
  if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
  }
  if (get_field('is_screenshot')) :
    /* Render screenshot for example */
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/hero_package/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$sub_title = get_field('sub_title');
$title = get_field('title');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="content-wrapper flex-col">
    <?php if (function_exists('threeSixty_theme_breadcrumbs')) {
    threeSixty_theme_breadcrumbs();
  } ?>
    <?php if ($sub_title) { ?>
      <div class="white-color uppercase-text"><?= $sub_title ?></div>
    <?php } ?>
    <?php if ($title) { ?>
    <div class="text-xl white-color"><?= $title ?></div>
  <?php } ?>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
