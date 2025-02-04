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
$main_title = get_field('main_title');

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
        <div class="call-us same-style flex-col">
          <h3 class="text-xl bold same-title">Call us</h3>
          <div class="text-md same-description ">Mon-Fri from 8am to 5pm.</div>
          <div class="phone-number text-md medium">
          <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.2084 5.00008C13.0223 5.15889 13.7704 5.55696 14.3568 6.14336C14.9432 6.72976 15.3412 7.4778 15.5 8.29175M12.2084 1.66675C13.8994 1.85461 15.4764 2.61189 16.6802 3.81425C17.8841 5.01662 18.6434 6.59259 18.8334 8.28341M9.02253 11.5526C8.02121 10.5513 7.23055 9.41912 6.65056 8.21111C6.60067 8.1072 6.57572 8.05524 6.55656 7.9895C6.48846 7.75587 6.53737 7.46899 6.67905 7.27113C6.71892 7.21546 6.76655 7.16783 6.86181 7.07257C7.15315 6.78123 7.29881 6.63556 7.39405 6.48908C7.75322 5.93667 7.75321 5.22452 7.39405 4.67211C7.29881 4.52563 7.15315 4.37996 6.86181 4.08862L6.69942 3.92623C6.25655 3.48336 6.03511 3.26192 5.7973 3.14164C5.32433 2.90241 4.76577 2.90241 4.29281 3.14164C4.05499 3.26192 3.83355 3.48336 3.39069 3.92623L3.25932 4.05759C2.81797 4.49894 2.59729 4.71962 2.42875 5.01964C2.24174 5.35257 2.10727 5.86964 2.10841 6.25149C2.10943 6.59562 2.17618 6.8308 2.30969 7.30117C3.02716 9.82901 4.38089 12.2143 6.37088 14.2043C8.36086 16.1943 10.7462 17.548 13.274 18.2655C13.7444 18.399 13.9795 18.4657 14.3237 18.4668C14.7055 18.4679 15.2226 18.3334 15.5555 18.1464C15.8555 17.9779 16.0762 17.7572 16.5176 17.3158L16.6489 17.1845C17.0918 16.7416 17.3132 16.5202 17.4335 16.2824C17.6728 15.8094 17.6728 15.2508 17.4335 14.7779C17.3132 14.5401 17.0918 14.3186 16.6489 13.8758L16.4865 13.7134C16.1952 13.422 16.0495 13.2764 15.9031 13.1811C15.3506 12.8219 14.6385 12.822 14.0861 13.1811C13.9396 13.2764 13.7939 13.422 13.5026 13.7134C13.4073 13.8086 13.3597 13.8562 13.304 13.8961C13.1062 14.0378 12.8193 14.0867 12.5857 14.0186C12.5199 13.9994 12.468 13.9745 12.3641 13.9246C11.156 13.3446 10.0238 12.554 9.02253 11.5526Z" stroke="#A15C07" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          +966 (530) 911 360
        </div>
        </div>
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
