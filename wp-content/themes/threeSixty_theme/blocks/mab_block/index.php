<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'mab_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/mab_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$title = get_field('title');
$description = get_field('description');
$cta_link = get_field('cta_link');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="content-wrapper flex-col">
    <?php if (!empty($cta_link) && is_array($cta_link)) { ?>
      <a class="theme-cta-button content-wrapper-btn btn-white" href="<?= $cta_link['url'] ?>" target="<?= $cta_link['target'] ?>">
        <?= $cta_link['title'] ?>
      </a>
    <?php } ?>
    <?php if ($title): ?>
      <h4 class="title semi-bold"><?= $title ?></h4>
    <?php endif; ?>
    <?php if ($description): ?>
      <div class=" text-xl description"><?= $description ?></div>
    <?php endif; ?>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
