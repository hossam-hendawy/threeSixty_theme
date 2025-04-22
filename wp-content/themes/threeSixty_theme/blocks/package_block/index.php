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
$package_includes_icon = get_field('package_includes_icon', $post_id);
$get_started = get_field('get_started', $post_id);
$note = get_field('note', $post_id);
$note_icon = get_field('note_icon', $post_id);
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="content-wrapper">
    <div class="left-content-wrapper flex-col">
      <?php if (have_rows('service_benefits')) { ?>
        <?php while (have_rows('service_benefits')) {
          the_row();
          $icon = get_sub_field('icon' , $post_id);
          $title = get_sub_field('title' , $post_id);
          $description = get_sub_field('description' , $post_id);
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
            <div class="price-container">
              <sub class="d-md-h4 semi-bold gray-600">$</sub>
              <div class="price d-lg-h3 bold gray-600"><?= $package_price ?></div>
            </div>
          <?php endif; ?>
        </div>
        <div class="package-includes">
          <div class="package-includes-title text-md semi-bold">
            <?= $text_label = t('This package includes:', 'text', 'This package includes: Label'); ?>
          </div>
          <div class="package-includes-wrapper">
            <?php if (have_rows('package_includes', $post_id)) { ?>
              <?php while (have_rows('package_includes', $post_id)) {
                the_row();
                $text = get_sub_field('text');
                ?>
                <div class="text">
                  <?php if (!empty($package_includes_icon) && is_array($package_includes_icon)) { ?>
                    <picture class="icon">
                      <img src="<?= $package_includes_icon['url'] ?>" alt="<?= $package_includes_icon['alt'] ?>">
                    </picture>
                  <?php } ?>
                  <div class="the-text text-md medium"><?= $text ?></div>
                </div>
              <?php } ?>
            <?php } ?>
          </div>
        </div>
        <div class="cta-button-wrapper">
          <?php if (!empty($get_started) && is_array($get_started)) { ?>
            <a class="theme-cta-button uppercase-text" href="<?= $get_started['url'] ?>" target="<?= $get_started['target'] ?>">
              <?= $get_started['title'] ?>
              <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
                <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"/>
                <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"/>
              </svg>
            </a>
          <?php } ?>
        </div>
      </div>
      <div class="note-wrapper">
        <?php if (!empty($note_icon) && is_array($note_icon)) { ?>
          <picture class="note-icon-wrapper">
            <img src="<?= $note_icon['url'] ?>" alt="<?= $note_icon['alt'] ?>">
          </picture>
        <?php } ?>
        <?php if ($note): ?>
          <div class="note-text text-sm regular"><?= $note ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
