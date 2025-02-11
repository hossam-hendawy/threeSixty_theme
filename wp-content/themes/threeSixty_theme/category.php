<?php
/**
 * A Simple Category Template
 */
/*w*/
$category = get_queried_object();
get_header(); ?>
<section id="recent_posts" data-section-class="recent_posts" class="threeSixty_theme-block recent_posts collection-block">
  <div class="container">
    <h5 class="semi-bold recent-content-title"><?= single_tag_title(); ?></h5>
    <?php if (tag_description()) { ?>
      <div class="text-xl gray-500"><?= tag_description() ?></div>
    <?php } ?>
    <?php
    $paged = (get_query_var('page_val') ? get_query_var('page_val') : 1);
    $args = array(
      'paged' => $paged,
      'tag' => $category->slug,
      'posts_per_page' => 9,
      'order' => 'DESC',
      'orderby' => 'date',
      'post_status' => 'publish'
    );
    $the_query = new WP_Query($args);
    $have_posts = $the_query->have_posts();
    ?>
    <?php if ($have_posts) { ?>
      <div class="swiper recent-posts-swiper">
        <div class="swiper-wrapper">
          <?php while ($the_query->have_posts()) {
            $the_query->the_post();
            get_template_part("partials/recent-card", '', array('post_id' => get_the_id()));
          }
          /* Restore original Post Data */
          wp_reset_postdata(); ?>
        </div>
      </div>
      <?php
      // Check if there are more than 3 posts
      if ($the_query->found_posts > 3) { ?>
        <div class="swiper-navigations">
          <div class="swiper-button-prev swiper-navigation arrow" role="button" tabindex="0" aria-label="Previous Slide">
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
              <path class="border" d="M0.5 0.5H55.5V55.5H0.5V0.5Z" stroke="Red" />
              <path class="arrow" d="M35 28H21M21 28L28 35M21 28L28 21" stroke="#475467" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <div class="swiper-button-next swiper-navigation arrow" role="button" tabindex="0" aria-label="Next Slide">
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
              <path class="border" d="M0.5 0.5H55.5V55.5H0.5V0.5Z" stroke="#98A2B3" />
              <path class="arrow" d="M21 28H35M35 28L28 21M35 28L28 35" stroke="#475467" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
        </div>
      <?php } ?>
    <?php } else {
      echo '<h3 class="wp-collection-no-posts headline-3 text-center">No Posts For This Tag</h3>';
    } ?>
  </div>
</section>
<?php get_footer(); ?>
