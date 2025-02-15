<?php
// @author HOSSAM
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'our_services_details_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/our_services_details_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="overview-content gab-20 flex-col">
    <?php if ($sub_title): ?>
      <h2 class="text-xl gold center-text sub-title"><?= $sub_title ?></h2>
    <?php endif; ?>
    <?php if ($title): ?>
      <h3 class="d-lg-h3 gray-950 bold center-text overview-title"><?= $title ?></h3>
    <?php endif; ?>
    <?php if ($description): ?>
      <div class="text-xl gray-500 center-text overview-description"><?= $description ?></div>
    <?php endif; ?>
  </div>
  <?php if (have_rows('service_benefits')) { ?>
    <div class="services-cards">
      <?php while (have_rows('service_benefits')) {
        the_row();
        $icon = get_sub_field('icon');
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        ?>
        <div class="services-card">
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
    </div>
  <?php } ?>
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
              <a class="theme-cta-button offering-btn btn-white" href="<?= $offering_link['url'] ?>" target="<?= $offering_link['target'] ?>">
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
