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


?>
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="cards-wrapper">
    <div class="left-content">
      <div class="flex-col gab-20 content">
        <?php if ($title) { ?>
        <h5 class="bold title"><?= $title ?></h5>
        <?php } ?>
        <?php if ($description) { ?>
        <div class="text-xl"><?= $description ?></div>
        <?php } ?>
      </div>
      <?php if (have_rows('step')) { ?>
      <div class="steps-cards flex-col">
        <?php while (have_rows('step')) {
          the_row();
          $info_title = get_sub_field('info_title');
          $info_description = get_sub_field('info_description');
          $index = get_row_index();
          ?>
        <div class="step-card">
          <div class="left-step"><?= $index ?></div>
          <div class="right-step flex-col">
            <?php if ($info_title) { ?>
            <h4 class="text-xl bold info-title"><?= $info_title ?></h4>
            <?php } ?>
            <?php if ($info_description) { ?>
            <div class="info-description text-md"><?= $info_description ?></div>
            <?php } ?>
          </div>
        </div>
        <?php } ?>
      </div>
      <?php } ?>
    </div>
    <div class="right-content">
      <div class="get-in-touch"></div>
      <div class="bottom-content">
        <div class="support same-style flex-col">
          <h3 class="text-xl bold same-title">Customer Support</h3>
          <div class="text-md same-description">Speak to our friendly team.</div>
          <div class="phone-number text-md medium">
            <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M8.11388 7.61365L4.60777 4.10754M4.60777 15.8927L8.1403 12.3601M12.8845 12.3866L16.3907 15.8927M16.3907 4.10754L12.8576 7.6406M18.8337 10.0001C18.8337 14.6025 15.1027 18.3334 10.5003 18.3334C5.89795 18.3334 2.16699 14.6025 2.16699 10.0001C2.16699 5.39771 5.89795 1.66675 10.5003 1.66675C15.1027 1.66675 18.8337 5.39771 18.8337 10.0001ZM13.8337 10.0001C13.8337 11.841 12.3413 13.3334 10.5003 13.3334C8.65938 13.3334 7.16699 11.841 7.16699 10.0001C7.16699 8.15913 8.65938 6.66675 10.5003 6.66675C12.3413 6.66675 13.8337 8.15913 13.8337 10.0001Z" stroke="#A15C07" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            support@3sixty.sa
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
