<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'contact_us_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/contact_us_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$title = get_field('title');
$description = get_field('description');
$get_in_touch_title = get_field('get_in_touch_title');
$get_in_touch_description = get_field('get_in_touch_description');
$form = get_field('form');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="cards-wrapper">
    <div class="left-content">
      <div class="flex-col gab-20 content">
        <?php if ($title) { ?>
          <h5 class="bold title iv-st-from-bottom"><?= $title ?></h5>
        <?php } ?>
        <?php if ($description) { ?>
          <div class="text-xl iv-st-from-bottom"><?= $description ?></div>
        <?php } ?>
      </div>
      <?php if (have_rows('step')) { ?>
        <div class="steps-cards iv-st-from-bottom  flex-col">
          <?php while (have_rows('step')) {
            the_row();
            $info_title = get_sub_field('info_title');
            $info_description = get_sub_field('info_description');
            $index = get_row_index();
            ?>
            <div class="step-card ">
              <div class="left-step"><?= $index ?></div>
              <div class="right-step flex-col">
                <?php if ($info_title) { ?>
                  <h4 class="text-xl bold info-title"><?= $info_title ?></h4>
                <?php } ?>
                <?php if ($info_description) { ?>
                  <div
                    class="info-description gray-500 text-md"><?= $info_description ?></div>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
    <div class="right-content iv-st-from-bottom">
      <div class="get-in-touch">
        <?php if ($get_in_touch_title) { ?>
          <h3 class="get-in-touch-title bold"><?= $get_in_touch_title ?></h3>
        <?php } ?>
        <?php if ($get_in_touch_description) { ?>
          <div
            class="get-in-touch-description text-xl"><?= $get_in_touch_description ?></div>
        <?php } ?>
        <div class="form-wrapper">
          <?= $form ?>
        </div>
      </div>
      <?php if (have_rows('information_card')) { ?>
        <div class="bottom-content">
          <?php while (have_rows('information_card')) {
            the_row();
            $information_title = get_sub_field('information_title');
            $information_description = get_sub_field('information_description');
            $icon = get_sub_field('icon');
            $text = get_sub_field('text');
            ?>
            <div class="information-support flex-col iv-st-from-bottom">
              <?php if ($information_title) { ?>
                <h3
                  class="text-xl bold information-title"><?= $information_title ?></h3>
              <?php } ?>
              <?php if ($information_description) { ?>
                <div
                  class="text-md information-description"><?= $information_description ?></div>
              <?php } ?>
              <?php if (have_rows('contact_info')) { ?>
                <?php while (have_rows('contact_info')) {
                  the_row();
                  $icon = get_sub_field('icon');
                  $text = get_sub_field('info_text');
                  ?>
                  <div class="info-detail text-md medium">
                    <?php if ($icon) { ?>
                      <picture class="icon cover-image">
                        <img src="<?= $icon['url'] ?>"
                             alt="<?= $icon['alt'] ?>">
                      </picture>
                    <?php } ?>
                    <?php if ($text) { ?>
                      <div class="info-text">
                        <?= $text ?>
                      </div>
                    <?php } ?>
                  </div>
                <?php }
              } ?>
            </div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
