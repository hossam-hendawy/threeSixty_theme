<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'about_us_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/about_us_block/screenshot.png" >';

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
<div class="isolation-mode">
  <svg width="449" height="518" viewBox="0 0 449 518" fill="none">
    <g opacity="0.8" clip-path="url(#clip0_243_7188)">
      <path d="M149.575 518L449 517.195L298.589 258.734L149.575 518Z" fill="url(#paint0_linear_243_7188)"/>
      <path d="M0 259.022L149.475 0H449L298.589 258.734L149.575 518L0 259.022Z" fill="url(#paint1_linear_243_7188)"/>
    </g>
    <defs>
      <linearGradient id="paint0_linear_243_7188" x1="299" y1="259" x2="235.178" y2="501.181"
                      gradientUnits="userSpaceOnUse">
        <stop/>
        <stop offset="1" stop-color="#3E4A5B"/>
      </linearGradient>
      <linearGradient id="paint1_linear_243_7188" x1="158.223" y1="-2.03715e-06" x2="150.111" y2="517.873"
                      gradientUnits="userSpaceOnUse">
        <stop/>
        <stop offset="1" stop-color="#536279"/>
      </linearGradient>
      <clipPath id="clip0_243_7188">
        <rect width="449" height="518" fill="white"/>
      </clipPath>
    </defs>
  </svg>
</div>
<div class="container">
  <div class="content-wrapper flex-col">
    <?php if (function_exists('threeSixty_theme_breadcrumbs')) {
      threeSixty_theme_breadcrumbs();
    } ?>
    <?php if ($title) { ?>
      <h1 class="d-xl-h2 white-color bold uppercase-text"><?= $title ?></h1>
    <?php } ?>
    <?php if ($description) { ?>
      <div class="text-xl white-color blog-description"><?= $description ?></div>
    <?php } ?>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
