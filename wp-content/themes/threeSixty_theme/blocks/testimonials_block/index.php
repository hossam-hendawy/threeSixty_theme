<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'testimonials_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/testimonials_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$title = get_field('title');
$description = get_field('description');
$programmatic_or_manual = get_field("programmatic_or_manual");
if ($programmatic_or_manual === 'programmatic') {
  $query_options = get_field("query_options") ?: [];
  $number_of_posts = isset($query_options['number_of_posts']) ? (int)$query_options['number_of_posts'] : 3;
  if ($number_of_posts > 3) {
    $number_of_posts = 3;
  }
  $order = isset($query_options['order']) && in_array($query_options['order'], ['asc', 'desc']) ? $query_options['order'] : 'DESC';
  $args = [
    "post_type" => "testimonials",
    "posts_per_page" => $number_of_posts,
    "order" => $order,
    "post_status" => "publish",
    "paged" => 1,
    'orderby' => 'date',
  ];
  $the_query = new WP_Query($args);
}
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="small-content">
  <?php if ($title): ?>
    <h3 class="text-xl title"><?= $title ?></h3>
  <?php endif; ?>
  <?php if ($description): ?>
    <div class="d-lg-h3 description"><?= $description ?></div>
  <?php endif; ?>
</div>
<div class="cards-wrapper">
  <div class="large-content">
    <?php if ($title): ?>
      <h3 class="text-xl title"><?= $title ?></h3>
    <?php endif; ?>
    <?php if ($description): ?>
      <div class="d-lg-h3 description"><?= $description ?></div>
    <?php endif; ?>
    <div class="swiper-navigations testimonial-navigations large-screen">
      <div class="swiper-button-prev swiper-navigation arrow" role="button" tabindex="0" aria-label="Previous Slide">
        <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
          <path class="border" d="M0.5 0.5H55.5V55.5H0.5V0.5Z" stroke="Red"/>
          <path class="arrow" d="M35 28H21M21 28L28 35M21 28L28 21" stroke="#475467" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="swiper-button-next swiper-navigation arrow" role="button" tabindex="0" aria-label="Next Slide">
        <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
          <path class="border" d="M0.5 0.5H55.5V55.5H0.5V0.5Z" stroke="#98A2B3"/>
          <path class="arrow" d="M21 28H35M35 28L28 21M35 28L28 35" stroke="#475467" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>
  </div>
  <?php if ($programmatic_or_manual === "manual") { ?>
    <div class="swiper testimonials-swiper">
      <div class="swiper-wrapper">
        <?php
        $cards = get_field("testimonial_card");
        if (is_array($cards)) {
          foreach ($cards as $card) {
            get_template_part("partials/testimonial_card", "", ["post_id" => $card->ID]);
          }
        }
        ?>
      </div>
    </div>
  <?php } elseif (isset($the_query) && $the_query->have_posts()) { ?>
    <div class="swiper testimonials-swiper">
      <div class="swiper-wrapper">
        <?php while ($the_query->have_posts()) {
          $the_query->the_post();
          get_template_part("partials/testimonial_card", "", ["post_id" => get_the_ID()]);
        } ?>
        <?php wp_reset_postdata(); ?>
      </div>
    </div>
  <?php } ?>
</div>
<svg class="logo" width="693" height="137" viewBox="0 0 693 137" fill="none" aria-hidden="true">
  <path d="M0.354445 14.5341V6.18182H74.1826V14.5341H41.9169V108H32.6201V14.5341H0.354445ZM99.3229 60.2727V108H90.4735V6.18182H99.3229V43.6179H100.118C101.908 39.6738 104.676 36.5251 108.421 34.1719C112.199 31.8187 116.972 30.642 122.739 30.642C127.91 30.642 132.45 31.7027 136.361 33.8239C140.272 35.9451 143.321 39.0772 145.509 43.2202C147.696 47.3632 148.79 52.4673 148.79 58.5327V108H139.891V59.0795C139.891 52.7822 138.134 47.8272 134.621 44.2145C131.141 40.5687 126.435 38.7457 120.502 38.7457C116.425 38.7457 112.796 39.6075 109.614 41.331C106.432 43.0544 103.913 45.5402 102.057 48.7884C100.234 52.0033 99.3229 55.8314 99.3229 60.2727ZM169.295 108V31.6364H177.895V43.3693H178.542C180.066 39.5246 182.718 36.4257 186.496 34.0724C190.308 31.6861 194.617 30.4929 199.422 30.4929C200.152 30.4929 200.964 30.5095 201.858 30.5426C202.753 30.5758 203.499 30.6089 204.096 30.642V39.6406C203.698 39.5743 203.002 39.4749 202.008 39.3423C201.013 39.2098 199.936 39.1435 198.776 39.1435C194.799 39.1435 191.252 39.9886 188.137 41.679C185.055 43.3362 182.618 45.6397 180.829 48.5895C179.039 51.5393 178.144 54.9034 178.144 58.6818V108H169.295ZM244.946 109.591C237.82 109.591 231.656 107.934 226.452 104.619C221.248 101.272 217.221 96.6482 214.371 90.7486C211.554 84.8158 210.145 77.9882 210.145 70.2656C210.145 62.5762 211.554 55.7486 214.371 49.7827C217.221 43.7836 221.149 39.0937 226.154 35.7131C231.192 32.2992 237.008 30.5923 243.604 30.5923C247.747 30.5923 251.741 31.3546 255.585 32.8793C259.43 34.3707 262.877 36.6742 265.926 39.7898C269.009 42.8722 271.445 46.7666 273.235 51.473C275.024 56.1463 275.919 61.6813 275.919 68.0781V72.4531H216.26V64.6477H266.871C266.871 59.7424 265.877 55.3343 263.888 51.4233C261.933 47.4792 259.198 44.3636 255.685 42.0767C252.205 39.7898 248.178 38.6463 243.604 38.6463C238.765 38.6463 234.506 39.9389 230.827 42.5241C227.148 45.1094 224.264 48.5232 222.176 52.7656C220.121 57.008 219.077 61.6482 219.044 66.6861V71.3594C219.044 77.4247 220.088 82.7277 222.176 87.2685C224.298 91.776 227.297 95.2727 231.175 97.7585C235.053 100.244 239.643 101.487 244.946 101.487C248.559 101.487 251.724 100.924 254.442 99.7969C257.193 98.67 259.496 97.1619 261.353 95.2727C263.242 93.3504 264.667 91.2457 265.628 88.9588L274.03 91.6932C272.87 94.9081 270.964 97.8745 268.313 100.592C265.694 103.31 262.413 105.498 258.469 107.155C254.558 108.779 250.05 109.591 244.946 109.591ZM323.494 109.591C316.368 109.591 310.203 107.934 305 104.619C299.796 101.272 295.769 96.6482 292.919 90.7486C290.101 84.8158 288.693 77.9882 288.693 70.2656C288.693 62.5762 290.101 55.7486 292.919 49.7827C295.769 43.7836 299.697 39.0937 304.701 35.7131C309.739 32.2992 315.556 30.5923 322.152 30.5923C326.295 30.5923 330.288 31.3546 334.133 32.8793C337.978 34.3707 341.425 36.6742 344.474 39.7898C347.556 42.8722 349.993 46.7666 351.782 51.473C353.572 56.1463 354.467 61.6813 354.467 68.0781V72.4531H294.808V64.6477H345.419C345.419 59.7424 344.424 55.3343 342.436 51.4233C340.48 47.4792 337.746 44.3636 334.233 42.0767C330.752 39.7898 326.725 38.6463 322.152 38.6463C317.313 38.6463 313.054 39.9389 309.375 42.5241C305.696 45.1094 302.812 48.5232 300.724 52.7656C298.669 57.008 297.625 61.6482 297.592 66.6861V71.3594C297.592 77.4247 298.636 82.7277 300.724 87.2685C302.845 91.776 305.845 95.2727 309.723 97.7585C313.6 100.244 318.191 101.487 323.494 101.487C327.107 101.487 330.272 100.924 332.99 99.7969C335.741 98.67 338.044 97.1619 339.9 95.2727C341.789 93.3504 343.215 91.2457 344.176 88.9588L352.578 91.6932C351.418 94.9081 349.512 97.8745 346.86 100.592C344.242 103.31 340.961 105.498 337.017 107.155C333.106 108.779 328.598 109.591 323.494 109.591ZM423.569 35.4645C423.171 31.4541 421.464 28.3385 418.448 26.1179C415.432 23.8972 411.338 22.7869 406.168 22.7869C402.655 22.7869 399.688 23.2841 397.269 24.2784C394.849 25.2396 392.993 26.5819 391.701 28.3054C390.441 30.0289 389.811 31.9844 389.811 34.1719C389.745 35.9948 390.126 37.5857 390.955 38.9446C391.817 40.3035 392.993 41.4801 394.485 42.4744C395.976 43.4356 397.7 44.2808 399.655 45.0099C401.611 45.706 403.699 46.3026 405.919 46.7997L415.067 48.9872C419.508 49.9815 423.585 51.3073 427.297 52.9645C431.009 54.6217 434.224 56.66 436.942 59.0795C439.66 61.4991 441.765 64.3494 443.256 67.6307C444.781 70.9119 445.56 74.6738 445.593 78.9162C445.56 85.1473 443.969 90.5497 440.82 95.1236C437.704 99.6643 433.197 103.194 427.297 105.713C421.431 108.199 414.355 109.442 406.069 109.442C397.849 109.442 390.69 108.182 384.591 105.663C378.526 103.144 373.786 99.4157 370.373 94.4773C366.992 89.5057 365.219 83.3575 365.053 76.0327H385.884C386.116 79.4465 387.094 82.2969 388.817 84.5838C390.574 86.8376 392.91 88.5445 395.827 89.7045C398.777 90.8314 402.108 91.3949 405.82 91.3949C409.466 91.3949 412.631 90.8646 415.316 89.804C418.034 88.7434 420.138 87.2685 421.63 85.3793C423.121 83.4901 423.867 81.3191 423.867 78.8665C423.867 76.5795 423.187 74.6572 421.829 73.0994C420.503 71.5417 418.547 70.2159 415.962 69.1222C413.41 68.0284 410.278 67.0341 406.566 66.1392L395.479 63.3551C386.895 61.267 380.117 58.0024 375.145 53.5611C370.174 49.1198 367.704 43.1373 367.738 35.6136C367.704 29.4489 369.345 24.063 372.659 19.456C376.007 14.849 380.597 11.2528 386.431 8.66761C392.264 6.08238 398.893 4.78977 406.317 4.78977C413.874 4.78977 420.47 6.08238 426.104 8.66761C431.772 11.2528 436.18 14.849 439.329 19.456C442.477 24.063 444.101 29.3991 444.201 35.4645H423.569ZM456.875 108V31.6364H478.054V108H456.875ZM467.514 21.7926C464.365 21.7926 461.664 20.7486 459.41 18.6605C457.19 16.5393 456.079 14.0038 456.079 11.054C456.079 8.13731 457.19 5.63494 459.41 3.54688C461.664 1.42566 464.365 0.365052 467.514 0.365052C470.663 0.365052 473.347 1.42566 475.568 3.54688C477.822 5.63494 478.949 8.13731 478.949 11.054C478.949 14.0038 477.822 16.5393 475.568 18.6605C473.347 20.7486 470.663 21.7926 467.514 21.7926ZM509.918 31.6364L523.938 58.3338L538.306 31.6364H560.032L537.908 69.8182L560.628 108H539.002L523.938 81.6009L509.123 108H487.248L509.918 69.8182L488.043 31.6364H509.918ZM610.428 31.6364V47.5455H564.441V31.6364H610.428ZM574.881 13.3409H596.06V84.5341C596.06 86.4896 596.358 88.0142 596.955 89.108C597.551 90.1686 598.38 90.9143 599.441 91.3452C600.534 91.776 601.794 91.9915 603.219 91.9915C604.213 91.9915 605.208 91.9086 606.202 91.7429C607.196 91.544 607.959 91.3949 608.489 91.2955L611.82 107.055C610.759 107.387 609.268 107.768 607.345 108.199C605.423 108.663 603.086 108.945 600.335 109.044C595.231 109.243 590.757 108.563 586.912 107.006C583.101 105.448 580.134 103.028 578.013 99.7472C575.892 96.4659 574.848 92.3229 574.881 87.3182V13.3409ZM634.499 136.636C631.815 136.636 629.296 136.421 626.942 135.99C624.622 135.592 622.7 135.079 621.175 134.449L625.948 118.639C628.434 119.402 630.671 119.816 632.66 119.882C634.682 119.948 636.422 119.484 637.88 118.49C639.371 117.496 640.581 115.805 641.509 113.419L642.752 110.188L615.359 31.6364H637.631L653.441 87.7159H654.236L670.195 31.6364H692.617L662.937 116.253C661.512 120.363 659.573 123.942 657.12 126.991C654.701 130.074 651.635 132.444 647.923 134.101C644.21 135.791 639.736 136.636 634.499 136.636Z"
        fill="#475467" fill-opacity="0.05"/>
</svg>
<div class="swiper-navigations testimonial-navigations small-screen">
  <div class="swiper-button-prev swiper-navigation arrow" role="button" tabindex="0" aria-label="Previous Slide">
    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
      <path class="border" d="M0.5 0.5H55.5V55.5H0.5V0.5Z" stroke="Red"/>
      <path class="arrow" d="M35 28H21M21 28L28 35M21 28L28 21" stroke="#475467" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
  <div class="swiper-button-next swiper-navigation arrow" role="button" tabindex="0" aria-label="Next Slide">
    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
      <path class="border" d="M0.5 0.5H55.5V55.5H0.5V0.5Z" stroke="#98A2B3"/>
      <path class="arrow" d="M21 28H35M35 28L28 21M35 28L28 35" stroke="#475467" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
