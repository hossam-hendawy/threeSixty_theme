<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'package_description';
$has_border = get_field('has_border');
$has_border = $has_border ? ' block-has-border' : ' ';
if (isset($block)) {
  $id = 'block_' . uniqid();
  if (!empty($block['anchor'])) {
    $id = $block['anchor'];
  }
  $className .= $has_border;
// Create class attribute allowing for custom "className" and "align" values.
  if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
  }
  if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
  }
  if (get_field('is_screenshot')) :
    /* Render screenshot for example */
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/package_description/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$position_image = get_field('position_image');
$title = get_field('title');
$description = get_field('description');
$image = get_field('image');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="cards-wrapper iv-st-from-bottom <?= $position_image === 'right' ? 'content-reverse' : "" ?>">
    <div class="left-content flex-col gab-40">
      <?php if ($title): ?>
        <h2 class="semi-bold title d-sm-h5"><?= $title ?></h2>
      <?php endif; ?>
      <?php if ($description): ?>
        <div class="text-lg description"><?= $description ?></div>
      <?php endif; ?>
    </div>
    <?php if (!empty($image) && is_array($image)) { ?>
      <div class="right-image">
        <picture class="image image-wrapper cover-image aspect-ratio">
          <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
        </picture>
      </div>
    <?php } ?>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
