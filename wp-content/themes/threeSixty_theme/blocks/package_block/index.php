<?php
// @author HOSSAM
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'package_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/package_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$post_id = get_the_ID();
$package_title = get_field('package_title', $post_id);
$package_excerpt = get_field('package_excerpt', $post_id);
$package_price = get_field('package_price', $post_id);
$package_icon = get_field('package_icon', $post_id);
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="content-wrapper">
    <div class="left-content-wrapper flex-col">
      <?php if (have_rows('service_benefits')) { ?>
        <?php while (have_rows('service_benefits')) {
          the_row();
          $icon = get_sub_field('icon');
          $title = get_sub_field('title');
          $description = get_sub_field('description');
          ?>
          <div class="service-benefit">
            <?php if (!empty($icon) && is_array($icon)) { ?>
              <picture class="icon-wrapper">
                <img src="<?= $icon['url'] ?>" alt="<?= $icon['alt'] ?>">
              </picture>
            <?php } ?>
            <div class="service-benefit-wrapper flex-col">
              <?php if ($title): ?>
                <div class="title text-xl bold"><?= $title ?></div>
              <?php endif; ?>
              <?php if ($description): ?>
                <div class="text-md description regular gray-500"><?= $description ?></div>
              <?php endif; ?>
            </div>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
    <div class="right-content-wrapper">
      <div class="package-box-wrapper">
        <div class="package-title-and-price">
          <div class="icon-and-package-title">
            <?php if (!empty($package_icon) && is_array($package_icon)) { ?>
              <picture class="package-icon-wrapper">
                <img src="<?= $package_icon['url'] ?>" alt="<?= $package_icon['alt'] ?>">
              </picture>
            <?php } ?>
            <div class="title-and-excerpt flex-col">
              <?php if ($package_title): ?>
                <div class="package-title d-xs-6 bold uppercase-text"><?= $package_title ?></div>
              <?php endif; ?>
              <?php if ($package_excerpt): ?>
                <div class="text-md description regular gray-500"><?= $package_excerpt ?></div>
              <?php endif; ?>
            </div>
          </div>
          <?php if ($package_price): ?>
            <div class="price d-lg-h3 bold gray-600"><?= $package_price ?></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
