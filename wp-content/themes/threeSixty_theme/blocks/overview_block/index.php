<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'overview_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/overview_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="overview-content gab-20 flex-col">
    <?php if ($sub_title): ?>
      <h2 class="text-xl gold center-text sub-title"><?= $sub_title ?></h2>
    <?php endif; ?>
    <?php if ($title): ?>
      <h3 class="d-lg-h3 gray-950 bold center-text overview-title"><?= $title ?></h3>
    <?php endif; ?>
    <?php if ($description): ?>
      <div class="text-xl gray-500 center-text overview-description"><?= $description ?></div>
    <?php endif; ?>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
