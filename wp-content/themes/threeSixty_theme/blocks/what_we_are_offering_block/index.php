<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'what_we_are_offering_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/what_we_are_offering_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
$programmatic_or_manual = get_field("programmatic_or_manual");
if ($programmatic_or_manual === 'programmatic') {
  $query_options = get_field("query_options") ?: [];
  $number_of_posts = isset($query_options['number_of_posts']) ? (int)$query_options['number_of_posts'] : -1;
  $order = isset($query_options['order']) && in_array($query_options['order'], ['asc', 'desc']) ? $query_options['order'] : 'DESC';
  $args = [
    "post_type" => "services",
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
<div class="container">
  <div class="top-content flex-col">
    <?php if ($sub_title): ?>
      <h1 class="text-xl sub-title text-center iv-st-from-bottom"><?= $sub_title ?></h1>
    <?php endif; ?>
    <?php if ($title): ?>
      <h3 class="bold title text-center iv-st-from-bottom"><?= $title ?></h3>
    <?php endif; ?>
    <?php if ($description): ?>
      <div class="text-lg description text-center iv-st-from-bottom"><?= $description ?></div>
    <?php endif; ?>
  </div>

  <?php if (have_rows('offering')) { ?>
    <div class="swiper-wrapper iv-st-from-bottom">
      <?php while (have_rows('offering')) {
        the_row();
        $offering_image = get_sub_field('offering_image');
        $offering_title = get_sub_field('offering_title');
        $offering_description = get_sub_field('offering_description');
        $button_icon = get_sub_field('button_icon');
        $offering_link = get_sub_field('offering_link');
        $offering_icon = get_sub_field('offering_icon');
        ?>
        <div class="swiper-slide offering-card">
          <div class="image-title flex-col">
            <?php if (!empty($offering_image) && is_array($offering_image)) { ?>
              <picture class="offering-image image-wrapper cover-image ">
                <img src="<?= $offering_image['url'] ?>" alt="<?= $offering_image['alt'] ?>">
              </picture>
            <?php } ?>
            <?php if ($offering_title) { ?>
              <div class="center-text offering-title d-xs-6"><?= $offering_title ?></div>
            <?php } ?>
          </div>
          <div class="description-btn flex-col">
            <?php if ($offering_description) { ?>
              <div class="description text-md regular center-text">
                <?= $offering_description ?>
              </div>
            <?php } ?>
            <?php if (!empty($offering_link) && is_array($offering_link)) { ?>
              <a class="theme-cta-button offering-btn btn-white" href="<?= $offering_link['url'] ?>" target="<?= $offering_link['target'] ?>">
                <?= $offering_link['title'] ?>
                <?php if ($offering_icon) { ?>
                  <picture class="icon">
                    <img src="<?= $offering_icon['url'] ?>" alt="<?= $offering_icon['alt'] ?>">
                  </picture>
                <?php } ?>
              </a>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
    </div>
  <?php } ?>

  <?php if ($programmatic_or_manual === "manual") { ?>
    <div class="swiper offering-cards iv-st-from-bottom">
      <div class="swiper-wrapper">
        <?php
        $cards = get_field("service_card");
        if (is_array($cards)) {
          foreach ($cards as $card) {
            get_template_part("partials/services-card", "", ["post_id" => $card->ID, "swiper-slide" => true]);
          }
        }
        ?>
      </div>
    </div>
  <?php } elseif (isset($the_query) && $the_query->have_posts()) { ?>
    <div class="swiper offering-cards iv-st-from-bottom">
      <div class="swiper-wrapper">
        <?php while ($the_query->have_posts()) {
          $the_query->the_post();
          get_template_part("partials/services-card", "",
            ["post_id" => get_the_ID(), "swiper-slide" => true]);
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
