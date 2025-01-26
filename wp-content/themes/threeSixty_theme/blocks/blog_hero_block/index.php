<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'blog_hero_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/blog_hero_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$title = get_field('title');
$description = get_field('description');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="content-wrapper flex-col">
    <?php if (function_exists('threeSixty_theme_breadcrumbs')) {
      threeSixty_theme_breadcrumbs();
    } ?>
    <?php if (!empty($cta_link) && is_array($cta_link)) { ?>
      <a class="theme-cta-link white-color" href="<?= $cta_link['url'] ?>" target="<?= $cta_link['target'] ?>"><?= $cta_link['title'] ?></a>
    <?php } ?>
    <?php if ($title) { ?>
      <h3 class="d-xl-h2 white-color bold"><?= $title ?></h3>
    <?php } ?>
    <?php if ($description) { ?>
      <div class="text-xl white-color blog-description"><?= $description ?></div>
    <?php } ?>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
