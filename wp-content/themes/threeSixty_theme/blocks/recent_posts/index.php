<?php
// @author
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'recent_posts';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/recent_posts/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$title = get_field('title');
$cta_button = get_field('cta_button');
$description = get_field('description');
$programmatic_or_manual = get_field("programmatic_or_manual");
if ($programmatic_or_manual === 'programmatic') {
  $query_options = get_field("query_options") ?: [];
  $number_of_posts = isset($query_options['number_of_posts']) ? (int)$query_options['number_of_posts'] : -1;
  $order = isset($query_options['order']) && in_array($query_options['order'], ['asc', 'desc']) ? $query_options['order'] : 'DESC';
  $args = [
    "post_type" => "post",
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
<div class="container recent_posts_block">
  <div class="cards-wrapper iv-st-from-bottom">
    <div class="recent-content flex-col">
      <?php if ($title) { ?>
        <h3 class="d-lg-h3 bold recent-content-title "><?= $title ?></h3>
      <?php } ?>
      <?php if ($description) { ?>
        <div class="text-xl gray-500"><?= $description ?></div>
      <?php } ?>
    </div>
    <?php if (!empty($cta_button) && is_array($cta_button)) { ?>
      <a class="theme-cta-button btn-white left-content-btn" href="<?= $cta_button['url'] ?>" target="<?= $cta_button['target'] ?>">
        <?= $cta_button['title'] ?>
        <svg width="25" height="29" viewBox="0 0 25 29" aria-hidden="true" fill="none">
          <g clip-path="url(#clip0_1377_4705)">
            <path d="M16.6718 29L1.08116e-05 28.9549L8.37476 14.4851L16.6718 29Z" fill="#CA8504"></path>
            <path d="M25 14.5012L16.6774 -3.63794e-07L1.90735e-06 -1.09278e-06L8.37476 14.4851L16.6717 29L25 14.5012Z" fill="#EAAA08"></path>
          </g>
          <defs>
            <clipPath id="clip0_1377_4705">
              <rect width="29" height="25" fill="white" transform="translate(25) rotate(90)"></rect>
            </clipPath>
          </defs>
        </svg>
      </a>
    <?php } ?>
  </div>
  <?php if ($programmatic_or_manual === "manual") { ?>
    <div class="swiper recent-posts-swiper recent-posts-in-home iv-st-from-bottom">
      <div class="swiper-wrapper">
        <?php
        $cards = get_field("post_card");
        if (is_array($cards)) {
          foreach ($cards as $card) {
            get_template_part("partials/recent-card", "", ["post_id" => $card->ID ?? null]);
          }
        }
        ?>
      </div>
    </div>
  <?php } elseif (isset($the_query) && $the_query->have_posts()) { ?>
    <div class="swiper recent-posts-swiper recent-posts-in-home iv-st-from-bottom">
      <div class="swiper-wrapper">
        <?php while ($the_query->have_posts()) {
          $the_query->the_post();
          get_template_part("partials/recent-card", "", ["post_id" => get_the_ID()]);
        } ?>
        <?php wp_reset_postdata(); ?>
      </div>
    </div>
  <?php } ?>
  <div class="swiper-navigations iv-st-from-bottom">
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
</section>
<!-- endregion threeSixty_theme's Block -->
