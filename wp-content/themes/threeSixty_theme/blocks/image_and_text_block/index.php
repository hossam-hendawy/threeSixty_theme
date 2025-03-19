<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'image_and_text_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/image_and_text_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$position_image = get_field('position_image');
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
$image = get_field('image');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="cards-wrapper <?= $position_image? 'content-reverse':"" ?>">
    <div class="left-content flex-col gab-20">
      <?php if ($sub_title): ?>
        <h1 class="text-xl sub-title iv-st-from-bottom"><?= $sub_title ?></h1>
      <?php endif; ?>
      <?php if ($title): ?>
        <h3 class="bold title iv-st-from-bottom"><?= $title ?></h3>
      <?php endif; ?>
      <?php if ($description): ?>
        <div class="text-lg description iv-st-from-bottom"><?= $description ?></div>
      <?php endif; ?>
    </div>
    <?php if (!empty($image) && is_array($image)) { ?>
    <div class="right-image">
        <picture class="image image-wrapper cover-image aspect-ratio iv-st-from-bottom">
          <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
        </picture>
    </div>
    <?php } ?>
  </div>
  </div>
</section>


<!-- endregion threeSixty_theme's Block -->
