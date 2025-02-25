<?php
// @author HOSSAM
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'tools_and_platforms_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/tools_and_platforms_block/screenshot.png" >';

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
<svg class="arrow" width="52" height="21" viewBox="0 0 52 21" fill="none" aria-hidden="true">
  <path d="M25.9978 20.4138L52 0.795374L25.9978 0.795373L1.31426e-06 0.795374L25.9978 20.4138Z" fill="black"/>
</svg>
<div class="container">
  <?php if (have_rows('logos')) { ?>
    <div class="autoplay-swiper-cont">
      <div class="autoplay-swiper">
        <div class="autoplay-swiper-wrapper">
          <?php while (have_rows('logos')) {
            the_row();
            $logo = get_sub_field('logo');
            ?>
            <?php if ($logo) { ?>
              <picture class="autoplay-swiper-slide">
                <img src="<?= $logo['url']; ?>" alt="<?= $logo['alt']; ?>">
              </picture>
            <?php } ?>
          <?php } ?>
        </div>
      </div>
    </div>
  <?php } ?>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
