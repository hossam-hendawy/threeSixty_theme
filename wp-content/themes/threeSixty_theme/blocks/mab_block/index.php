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
$label = get_field('label');
$image = get_field('image');
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
      <picture class="image cover-image aspect-ratio">
        <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
        <div class="circle hotspot-circle">
          <div class="hidden-content flex-col">
            <picture class="country-flag cover-image">
            </picture>
            <h5 class="text-xs semi-bold country-name">EGYPT, Cairo</h5>
            <h5 class="text-xs location">Ahmed Bin Asad Street Bir Uthman, Madinah 42331</h5>
            <svg class="hidden-content-svg" width="16" height="9" viewBox="0 0 16 9" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M14.0711 0.485289C14.962 0.485289 15.4081 1.56243 14.7782 2.1924L8.70711 8.26347C8.31658 8.654 7.68342 8.654 7.29289 8.26347L1.22183 2.1924C0.591867 1.56243 1.03803 0.485289 1.92894 0.485289L14.0711 0.485289Z" fill="white"/>
            </svg>
          </div>
        </div>
        <div class="circle hotspot-circle sa">
          <div class="hidden-content flex-col">
            <picture class="country-flag cover-image">
            </picture>
            <h5 class="text-xs semi-bold country-name">Saudi Arabia, Mah</h5>
            <h5 class="text-xs location">Ahmed Bin Asad Street Bir Uthman, Madinah 42331</h5>
            <svg class="hidden-content-svg" width="16" height="9" viewBox="0 0 16 9" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M14.0711 0.485289C14.962 0.485289 15.4081 1.56243 14.7782 2.1924L8.70711 8.26347C8.31658 8.654 7.68342 8.654 7.29289 8.26347L1.22183 2.1924C0.591867 1.56243 1.03803 0.485289 1.92894 0.485289L14.0711 0.485289Z" fill="white"/>
            </svg>
          </div>
        </div>
      </picture>
    <?php } ?>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
