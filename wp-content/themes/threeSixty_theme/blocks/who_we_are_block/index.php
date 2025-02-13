<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'who_we_are_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/who_we_are_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/;
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
$cta_button = get_field('cta_button');
$image = get_field('image');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="cards-wrapper">
    <div class="left-content flex-col gab-20">
      <?php if ($sub_title): ?>
        <h1 class="text-xl sub-title"><?= $sub_title ?></h1>
      <?php endif; ?>
      <?php if ($title): ?>
        <div class="title d-lg-h3"><?= $title ?></div>
      <?php endif; ?>
      <?php if ($description): ?>
        <div class="text-lg description"><?= $description ?></div>
      <?php endif; ?>
        <?php if (!empty($cta_button) && is_array($cta_button)) { ?>
          <a class="theme-cta-button btn-white left-content-btn" href="<?= $cta_button['url'] ?>" target="<?= $cta_button['target'] ?>">
            <?= $cta_button['title'] ?>
            <svg width="25" height="29" viewBox="0 0 25 29" aria-hidden="true" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_1377_4705)">
                <path d="M16.6718 29L1.08116e-05 28.9549L8.37476 14.4851L16.6718 29Z" fill="#CA8504"/>
                <path d="M25 14.5012L16.6774 -3.63794e-07L1.90735e-06 -1.09278e-06L8.37476 14.4851L16.6717 29L25 14.5012Z" fill="#EAAA08"/>
              </g>
              <defs>
                <clipPath id="clip0_1377_4705">
                  <rect width="29" height="25" fill="white" transform="translate(25) rotate(90)"/>
                </clipPath>
              </defs>
            </svg>
          </a>
        <?php } ?>
    </div>
    <div class="right-image">
      <?php if (!empty($image) && is_array($image)) { ?>
        <picture class="image image-wrapper cover-image aspect-ratio">
          <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
        </picture>
      <?php } ?>
    </div>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
