<?php
// @author HOSSAM
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'map_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/map_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$title = get_field('title');
$description = get_field('description');
$label = get_field('label');
$image = get_field('image');

$saudi_arabia_location = get_field('saudi_arabia_location');
$egypt_location = get_field('egypt_location');


$saudi_arabia_link = (is_array($saudi_arabia_location) && isset($saudi_arabia_location['link'])) ? $saudi_arabia_location['link'] : '';
$saudi_arabia_flag = (is_array($saudi_arabia_location) && isset($saudi_arabia_location['flag'])) ? $saudi_arabia_location['flag'] : '';
$saudi_arabia_county_name = (is_array($saudi_arabia_location) && isset($saudi_arabia_location['county_name'])) ? $saudi_arabia_location['county_name'] : '';
$saudi_arabia_description = (is_array($saudi_arabia_location) && isset($saudi_arabia_location['description'])) ? $saudi_arabia_location['description'] : '';


$egypt_location_link = (is_array($egypt_location) && isset($egypt_location['link'])) ? $egypt_location['link'] : '';
$egypt_location_flag = (is_array($egypt_location) && isset($egypt_location['flag'])) ? $egypt_location['flag'] : '';
$egypt_location_county_name = (is_array($egypt_location) && isset($egypt_location['county_name'])) ? $egypt_location['county_name'] : '';
$egypt_location_description = (is_array($egypt_location) && isset($egypt_location['description'])) ? $saudi_arabia_location['description'] : '';
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="content-wrapper flex-col">
    <?php if ($label) { ?>
      <div class="text-sm label-info medium"><?= $label ?></div>
    <?php } ?>
    <?php if ($title): ?>
      <h4 class="title semi-bold"><?= $title ?></h4>
    <?php endif; ?>
    <?php if ($description): ?>
      <div class=" text-xl description"><?= $description ?></div>
    <?php endif; ?>
  </div>
  <div class="image-wrapper">
    <?php if (!empty($image) && is_array($image)) { ?>
      <div class="image cover-image aspect-ratio">
        <img class="map-image" src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
        <div class="circle hotspot-circle">
          <a href="<?= $egypt_location_link ?>" class="hidden-content flex-col" target="_blank" aria-label="map (opens in a new tab)">
            <?php if (!empty($egypt_location_flag) && is_array($egypt_location_flag)): ?>
              <picture class="country-flag">
                <img src="<?= $egypt_location_flag['url'] ?>" alt="<?= $egypt_location_flag['alt'] ?>">
              </picture>
            <?php endif; ?>
            <?php if ($egypt_location_county_name): ?>
              <h5 class="text-xs semi-bold country-name"><?= $egypt_location_county_name ?></h5>
            <?php endif; ?>
            <?php if ($egypt_location_description): ?>
              <h5 class="text-xs location"><?= $egypt_location_description ?></h5>
            <?php endif; ?>
            <svg class="hidden-content-svg" width="16" height="9" viewBox="0 0 16 9" fill="none" aria-hidden="true">
              <path d="M14.0711 0.485289C14.962 0.485289 15.4081 1.56243 14.7782 2.1924L8.70711 8.26347C8.31658 8.654 7.68342 8.654 7.29289 8.26347L1.22183 2.1924C0.591867 1.56243 1.03803 0.485289 1.92894 0.485289L14.0711 0.485289Z" fill="white"/>
            </svg>
          </a>
        </div>
        <div class="circle hotspot-circle sa">
          <a href="<?= $saudi_arabia_link ?>" class="hidden-content flex-col" target="_blank" aria-label="map (opens in a new tab)">
            <?php if (!empty($saudi_arabia_flag) && is_array($saudi_arabia_flag)): ?>
              <picture class="country-flag">
                <img src="<?= $saudi_arabia_flag['url'] ?>" alt="<?= $saudi_arabia_flag['alt'] ?>">
              </picture>
            <?php endif; ?>
            <?php if ($saudi_arabia_county_name): ?>
              <h5 class="text-xs semi-bold country-name"><?= $saudi_arabia_county_name ?></h5>
            <?php endif; ?>
            <?php if ($saudi_arabia_description): ?>
              <h5 class="text-xs location"><?= $saudi_arabia_description ?></h5>
            <?php endif; ?>
            <svg class="hidden-content-svg" width="16" height="9" viewBox="0 0 16 9" fill="none" aria-hidden="true">
              <path d="M14.0711 0.485289C14.962 0.485289 15.4081 1.56243 14.7782 2.1924L8.70711 8.26347C8.31658 8.654 7.68342 8.654 7.29289 8.26347L1.22183 2.1924C0.591867 1.56243 1.03803 0.485289 1.92894 0.485289L14.0711 0.485289Z" fill="white"/>
            </svg>
          </a>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
