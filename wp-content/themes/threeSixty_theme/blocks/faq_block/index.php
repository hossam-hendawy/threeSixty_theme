<?php
// @author HOSSAM
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'faq_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/faq_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$title = get_field('title');
$description = get_field('description');
$cta = get_field('cta');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="faq-logo cover-image iv-st-from-bottom" role="img" aria-labelledby="hero-description">
  <svg viewBox="0 0 1790 611" fill="none" aria-hidden="true">
    <path
      d="M0.0582512 504V11.02H467.29V110.244H118.75V208.84H451.59V311.204H118.75V504H0.0582512ZM477.638 504L738.258 11.02H880.186L1130.13 504H1001.39L948.638 395.356H657.874L603.238 504H477.638ZM899.654 299.9C900.282 299.9 825.55 147.924 825.55 147.924L806.082 103.336L785.986 147.924L708.114 299.9H899.654ZM1618.58 610.76C1601.63 606.992 1563.95 592.548 1528.78 571.824C1497.38 552.984 1460.33 525.98 1446.51 510.28C1438.97 509.652 1412.6 506.512 1404.43 505.884C1348.54 500.232 1280.72 477.624 1232.99 426.756C1189.66 379.656 1171.45 330.672 1171.45 254.684C1171.45 187.488 1188.4 137.248 1227.97 92.032C1280.09 33.628 1361.1 0.971963 1480.42 0.971963C1599.11 0.971963 1678.24 29.86 1733.51 86.38C1774.95 129.084 1789.4 183.72 1789.4 254.684C1789.4 326.904 1763.02 392.216 1732.25 424.872C1699.59 459.412 1642.45 487.044 1599.74 493.324C1609.16 500.232 1623.61 505.884 1636.79 511.536C1652.49 519.072 1666.94 525.352 1682.64 527.864L1618.58 610.76ZM1480.42 416.08C1557.67 416.08 1602.88 399.124 1635.54 366.468C1660.66 341.348 1670.08 298.644 1670.08 256.568C1670.08 216.376 1662.54 173.044 1635.54 147.924C1603.51 117.152 1560.18 98.312 1479.79 98.312C1405.06 98.312 1356.71 118.408 1327.19 147.924C1302.07 173.044 1291.39 213.236 1291.39 257.196C1291.39 304.296 1302.07 345.744 1332.21 371.492C1367.38 401.008 1410.09 416.08 1480.42 416.08Z"
      fill="#302A30"/>
  </svg>
</div>
<div class="container">
  <div class="row justify-between content-wrapper iv-st-from-bottom">
    <div class="col-12 col-md-4">
      <div class="general-left-content">
        <?php if ($title) { ?>
          <div class="label"><?= $title ?></div>
        <?php } ?>
        <?php if ($description) { ?>
          <h2 class="serif-h2 description"><?= $description ?></h2>
        <?php } ?>
        <?php if (\Theme\Helpers::get_key_from_array('title', $cta)) { ?>
          <a href="<?= $cta['url'] ?>" target="<?= $cta['target'] ?>" class="theme-cta-button desktop-cta"><?= $cta['title'] ?></a>
        <?php } ?>
      </div>
    </div>
    <div class="col-12 col-md-8">
      <!--      region  tabs -->
      <?php if (have_rows('faqs_cards')) { ?>
        <div class="tabs hide-scrollbar">
          <?php
          $index = 1;
          while (have_rows('faqs_cards')) {
            the_row();
            $tab = get_sub_field('tab');
            ?>
            <?php if ($tab) { ?>
              <div class="tab <?= $index === 1 ? 'active' : '' ?>"
                   data-tab="<?= $index ?>"
                   role="button"
                   tabindex="0"
                   aria-label="Tab <?= $tab ?>"
                   aria-selected="<?= $index === 1 ? 'true' : 'false' ?>">
                <?= $tab ?>
              </div>
            <?php } ?>
            <?php
            $index++;
          }
          ?>
        </div>
      <?php } ?>
      <!--       endregion-->

      <!--      region tab content-->
      <?php if (have_rows('faqs_cards')) { ?>
        <?php
        $index = 1;
        while (have_rows('faqs_cards')) {
          the_row();
          $tab = get_sub_field('tab');
          $faqs_posts = get_sub_field('faq');
          ?>
          <div class="tab-content <?= $index === 1 ? 'active' : '' ?>" data-content="<?= $index ?>">
            <?php if ($faqs_posts) { ?>
              <div class="accordion">
                <?php foreach ($faqs_posts as $faq):
                  get_template_part("partials/faq-card", '', array('post_id' => $faq));
                  ?>
                <?php endforeach; ?>
              </div>
            <?php } ?>
          </div>
          <?php
          $index++;
        }
        ?>
      <?php } ?>
      <!-- endregion-->
    </div>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->






