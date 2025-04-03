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
  $className .= '  block-has-border ';
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
$marketing_icon = get_field('marketing_icon');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="overview-content flex-col iv-st-from-bottom">
    <?php if ($title): ?>
      <h4 class="bold overview-title"><?= $title ?></h4>
    <?php endif; ?>
    <?php if ($description): ?>
      <div
        class="text-xl gray-500  overview-description"><?= $description ?></div>
    <?php endif; ?>
  </div>
  <?php if (have_rows('marketing_services')) { ?>
    <div class="marketing-services-cards iv-st-from-bottom">
      <?php while (have_rows('marketing_services')) {
        the_row();
        $marketing_title = get_sub_field('marketing_title');
        $marketing_description = get_sub_field('marketing_description');
        ?>
        <div class="marketing-services-card">
          <?php if (!empty($marketing_icon) && is_array($marketing_icon)) { ?>
            <div class="icon-wrapper">
              <picture class="icon-wrapper-svg cover-image">
                <img src="<?= $marketing_icon['url'] ?>"
                     alt="<?= $marketing_icon['alt'] ?>">
              </picture>
            </div>
          <?php } ?>
          <div class="service-benefit-wrapper">
            <?php if ($marketing_title) { ?>
              <div class="title text-xl semi-bold"><?= $marketing_title ?></div>
            <?php } ?>
            <?php if ($marketing_description) { ?>
              <div
                class="text-md description regular gray-500"><?= $marketing_description ?></div>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
