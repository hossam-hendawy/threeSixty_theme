<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'service_hero';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/service_hero/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$image = get_field('image');
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<?php if (!empty($image) && is_array($image)) { ?>
  <picture class="isolation-mode">
    <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
  </picture>
<?php } ?>
<div class="container">
  <div class="content-wrapper flex-col">
    <?php if (function_exists('threeSixty_theme_breadcrumbs')) {
      threeSixty_theme_breadcrumbs();
    } ?>
    <div class="sub-title-and-title">
      <?php if ($sub_title) { ?>
        <div class="d-md-h4 fw-300 sub-title uppercase-text"><?= $sub_title ?></div>
      <?php } ?>
      <?php if ($title) { ?>
        <h1 class="d-xl-h2 fw-700 white-color uppercase-text"><?= $title ?></h1>
      <?php } ?>
    </div>
    <?php if ($description) { ?>
      <div class="description text-xl white-color regular"><?= $description ?></div>
    <?php } ?>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
