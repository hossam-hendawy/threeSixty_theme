<?php
// @author HOSSAM
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'hero_block';
if (isset($block)) {
  $id .= ' hero-block ';

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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/hero_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$title = get_field('title');
$sub_title = get_field('sub_title');
$location = get_field('location');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="iris-container">
  <svg xmlns="http://www.w3.org/2000/svg" width="525" height="300" viewBox="0 0 525 300" fill="none">
    <rect width="525" height="300" class="iris-shape" fill="black" x="0" y="0"/>
    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.9149 132.989C37.9309 80.3248 122.061 0 262.5 0C402.939 0 487.069 80.3248 520.085 132.989C526.638 143.442 526.638 156.558 520.085 167.011C487.069 219.675 402.939 300 262.5 300C122.061 300 37.9309 219.675 4.91489 167.011C-1.6383 156.558 -1.6383 143.442 4.9149 132.989ZM273.215 257.145C275.751 257.145 277.807 255.089 277.807 252.553V188.803L322.886 233.882C324.679 235.675 327.586 235.675 329.379 233.882L345.614 217.647C347.407 215.854 347.407 212.947 345.614 211.153L300.535 166.074H364.286C366.822 166.074 368.878 164.018 368.878 161.482V138.523C368.878 135.987 366.822 133.931 364.286 133.931H277.807V47.451C277.807 44.915 275.751 42.8591 273.215 42.8591L250.256 42.8591C247.72 42.8591 245.664 44.915 245.664 47.451V111.204L200.585 66.1243C198.792 64.331 195.884 64.331 194.091 66.1243L177.856 82.3589C176.063 84.1521 176.063 87.0595 177.856 88.8527L222.935 133.931H159.184C156.648 133.931 154.592 135.987 154.592 138.523V161.482C154.592 164.018 156.648 166.074 159.184 166.074H222.935L177.857 211.153C176.064 212.946 176.064 215.853 177.857 217.646L194.091 233.881C195.885 235.674 198.792 235.674 200.585 233.881L245.664 188.802V252.553C245.664 255.089 247.72 257.145 250.256 257.145H273.215ZM340.708 71.0612C331.049 61.4023 315.389 61.4023 305.73 71.0612L305.244 71.5465C295.586 81.2054 295.586 96.8655 305.244 106.524C314.903 116.183 330.563 116.183 340.222 106.524L340.708 106.039C350.367 96.3801 350.367 80.72 340.708 71.0612Z" fill="white"/>
  </svg>
  <div class="scroll-text">
    <div class="micro-text pulse-text">Scroll</div>
  </div>
</div>
<picture class="hero-cover-image hollywood-hero-background">
  <img src="<?= get_template_directory_uri() . '/images/backgrounds/Hollywood-hero-background.png' ?>" alt="Scenic view of the Hollywood hills during sunset with the famous Hollywood sign visible in the distance." aria-hidden="true">
</picture>
<picture class="hero-cover-image hollywood-hero-foreground">
  <img src="<?= get_template_directory_uri() . '/images/backgrounds/Hollywood-hero-foreground.png' ?>" alt="Foreground scenery showing detailed landscape elements of the Hollywood hills." aria-hidden="true">
</picture>
<div class="site-logo" role="img" aria-labelledby="hero-description" aria-label="Go to homepage">
  <svg width="1379" height="374" viewBox="0 0 1379 374" fill="none" aria-hidden="true">
    <title id="site-logo">Site logo</title>
    <path fill-rule="evenodd" clip-rule="evenodd"
          d="M1093.25 4.7868C1000.71 18.2312 949.453 66.7558 962.363 128.701C973.694 183.071 1018.38 203.337 1164.35 220.286C1270.27 232.585 1289.86 241.525 1281.72 273.838C1269.16 323.656 1065.14 312.313 1051.83 261.056L1050.37 255.439C1049.92 253.718 1048.38 252.507 1046.61 252.479L960.761 251.127C958.394 251.09 956.528 253.134 956.772 255.495L957.85 265.877C965.466 339.392 1047.34 377.55 1189.29 373.74C1321.75 370.186 1383.2 327.565 1374.09 245.574C1367.33 184.819 1330.02 166.396 1176.46 147.977C1078.32 136.206 1057.6 130.079 1052.95 111.465C1045.85 83.0639 1073.26 71.8104 1150.02 71.6171C1222.59 71.435 1261.91 84.3483 1268.98 110.693L1270.28 115.575C1270.74 117.281 1272.27 118.477 1274.03 118.505L1359.79 119.856C1362.18 119.894 1364.05 117.81 1363.77 115.43L1362.64 105.95C1353.64 30.1793 1232.24 -15.4066 1093.25 4.7868ZM0.939896 75.6198C0.939896 77.806 2.70666 79.5782 4.88607 79.5782H155.81C157.989 79.5782 159.756 81.3504 159.756 83.5366V365.209C159.756 367.394 161.523 369.166 163.702 369.166H247.331C249.51 369.166 251.277 367.394 251.277 365.209V83.5366C251.277 81.3504 253.044 79.5782 255.223 79.5782H406.147C408.326 79.5782 410.092 77.806 410.092 75.6198V11.1396C410.092 8.95347 408.326 7.18126 406.147 7.18126H4.88607C2.70666 7.18126 0.939896 8.95347 0.939896 11.1396V75.6198ZM510.56 365.209C510.56 367.394 512.326 369.166 514.505 369.166H867.315C869.494 369.166 871.26 367.394 871.26 365.209V300.728C871.26 298.543 869.494 296.769 867.315 296.769H600.644C598.462 296.769 596.696 294.997 596.696 292.812V228.361C596.696 226.182 598.451 224.414 600.622 224.402L855.303 223.052C857.443 223.041 859.184 221.322 859.228 219.177L860.565 156.047C860.612 153.837 858.843 152.016 856.639 152.005L601.886 150.655C599.746 150.644 598.007 148.925 597.96 146.781L596.627 83.6206C596.58 81.4021 598.36 79.5782 600.572 79.5782H861.931C864.11 79.5782 865.876 77.806 865.876 75.6198V11.1396C865.876 8.95347 864.11 7.18126 861.931 7.18126H514.505C512.326 7.18126 510.56 8.95347 510.56 11.1396V365.209Z"
          fill="white"/>
  </svg>
</div>
<picture class="drone cover-image">
  <img src="<?= get_template_directory_uri() . '/images/backgrounds/drone.png' ?>" alt="An image of a flying drone in the sky, showing its propellers and camera, symbolizing modern technology.">
</picture>
<div class="full-container">
  <div class="hero-content-wrapper">
    <div class="left-content">
      <?php if ($title) { ?>
        <h1 class="serif-h1 white-color"><?= $title ?></h1>
      <?php } ?>
      <?php if ($sub_title) { ?>
        <span class="micro-text text-uppercase white-color" aria-label="Sub-title"><?= $sub_title ?></span>
      <?php } ?>
    </div>
    <?php if ($location) { ?>
      <div class="right-content">
        <span class="micro-text text-uppercase white-color" aria-label="Location"><?= $location ?></span>
      </div>
    <?php } ?>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
