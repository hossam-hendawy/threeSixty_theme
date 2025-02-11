<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'package_description';
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
  <div class="cards-wrapper <?= $position_image === 'right' ? 'content-reverse' : "" ?>">
    <div class="left-content flex-col gab-40">
      <?php if ($title): ?>
        <h5 class="semi-bold title"><?= $title ?></h5>
      <?php endif; ?>
      <?php if ($description): ?>
        <div class="text-lg description"><?= $description ?></div>
      <?php endif; ?>
    </div>
    <div class="right-image">
      <?php if (!empty($image) && is_array($image)) { ?>
        <picture class="image image-wrapper cover-image aspect-ratio">
          <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
        </picture>
      <?php } ?>
    </div>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
