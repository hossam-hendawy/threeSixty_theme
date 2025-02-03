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
  <div class="cards-wrapper">
    <div class="recent-content flex-col">
      <?php if ($title) { ?>
        <h3 class="d-lg-h3 bold recent-content-title"><?= $title ?></h3>
      <?php } ?>
      <?php if ($description) { ?>
        <div class="text-xl gray-500"><?= $description ?></div>
      <?php } ?>
    </div>
    <?php if (!empty($cta_button) && is_array($cta_button)) { ?>
      <a class="theme-cta-button " href="<?= $cta_button['url'] ?>" target="<?= $cta_button['target'] ?>"><?= $cta_button['title'] ?></a>
    <?php } ?>
  </div>
  <?php if ($programmatic_or_manual === "manual") { ?>
    <div class="swiper recent-posts-swiper">
      <div class="swiper-wrapper">
        <?php foreach (get_field("recent_card") as $card):
          get_template_part("partials/recent-card", "", ["post_id" => $card->ID]);
        endforeach; ?>
      </div>
    </div>
  <?php } elseif (isset($the_query) && $the_query->have_posts()) { ?>
    <div class="swiper recent-posts-swiper">
      <div class="swiper-wrapper">
        <?php while ($the_query->have_posts()) {
          $the_query->the_post();
          get_template_part("partials/recent-card", "", ["post_id" => get_the_ID()]);
        } ?>
        <?php wp_reset_postdata(); ?>
      </div>
    </div>
  <?php } ?>
  <div class="swiper-navigations">
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
