<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'strategy_marketing_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/strategy_marketing_block/screenshot.png" >';

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
  <div class="overview-content flex-col">
    <?php if ($title): ?>
      <h4 class="bold overview-title"><?= $title ?></h4>
    <?php endif; ?>
    <?php if ($description): ?>
      <div class="text-xl gray-500  overview-description"><?= $description ?></div>
    <?php endif; ?>
  </div>
  <?php if (have_rows('marketing_services')) { ?>
    <div class="marketing-services-cards">
      <?php while (have_rows('marketing_services')) {
        the_row();
        $marketing_title = get_sub_field('marketing_title');
        $marketing_description = get_sub_field('marketing_description');
        ?>
        <div class="marketing-services-card">
          <picture class="icon-wrapper">
            <svg class="icon-wrapper-svg" width="24" height="21" viewBox="0 0 22 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_136_8237)">
                <path d="M14.4524 24L0.65509 23.9627L7.58592 11.9877L14.4524 24Z" fill="#CA8504"/>
                <path d="M21.3447 12.001L14.457 -3.01071e-07L0.655075 -9.04373e-07L7.58591 11.9877L14.4524 24L21.3447 12.001Z" fill="#EAAA08"/>
              </g>
              <defs>
                <clipPath id="clip0_136_8237">
                  <rect width="24" height="20.6897" fill="white" transform="translate(21.3447) rotate(90)"/>
                </clipPath>
              </defs>
            </svg>
          </picture>
          <div class="service-benefit-wrapper">
            <?php if ($marketing_title) { ?>
            <div class="title text-xl semi-bold"><?= $marketing_title ?></div>
            <?php } ?>
            <?php if ($marketing_description) { ?>
            <div class="text-md description regular gray-500"><?= $marketing_description ?></div>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
