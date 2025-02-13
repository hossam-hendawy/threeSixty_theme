<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'what_we_offer_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/what_we_offer_block/screenshot.png" >';

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
    <?php if (have_rows('offering')) { ?>
      <div class="offering-cards">
        <?php while (have_rows('offering')) {
          the_row();
          $offering_image = get_sub_field('offering_image');
          $offering_title = get_sub_field('offering_title');
          $offering_description = get_sub_field('offering_description');
          $button_icon = get_sub_field('button_icon');
          $offering_link = get_sub_field('offering_link');
          $offering_icon = get_sub_field('offering_icon');
          ?>
          <div class="offering-card">
            <div class="image-title flex-col">
              <?php if (!empty($offering_image) && is_array($offering_image)) { ?>
                  <picture class="offering-image image-wrapper cover-image ">
                    <img src="<?= $offering_image['url'] ?>" alt="<?= $offering_image['alt'] ?>">
                  </picture>
                <?php } ?>
              <?php if ($offering_title) { ?>
                <div class="center-text offering-title d-xs-6"><?= $offering_title ?></div>
              <?php } ?>
            </div>
            <div class="description-btn flex-col">
              <?php if ($offering_description) { ?>
                <div class="description text-md regular center-text">
                  <?= $offering_description ?>
                </div>
              <?php } ?>
              <?php if (!empty($offering_link) && is_array($offering_link)) { ?>
                <a class="theme-cta-button offering-btn" href="<?= $offering_link['url'] ?>" target="<?= $offering_link['target'] ?>">
                  <?= $offering_link['title'] ?>
                  <picture class="icon">
                    <img src="<?= $offering_icon['url'] ?>" alt="<?= $offering_icon['alt'] ?>">
                  </picture>
                </a>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
