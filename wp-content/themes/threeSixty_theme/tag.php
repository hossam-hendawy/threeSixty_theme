<?php
/**
 * A Simple Category Template
 */
$tags = get_queried_object();
get_header(); ?>
<section class="wp_collection_block"
         data-section-class="wp_collection_block">
  <div class="container">
    <h2 class="wp-collection-title  has-label"><?= single_tag_title(); ?></h2>
    <?php if (tag_description()) { ?>
      <div class="wp-collection-description paragraph"><?= tag_description() ?></div>
    <?php } ?>
    <?php
    $paged = (get_query_var('page_val') ? get_query_var('page_val') : 1);
    $args = array(
      'paged' => $paged,
      'tag' => $tags->slug,
      'posts_per_page' => 9,
      'order' => 'DESC',
      'orderby' => 'date',
      'post_status' => 'publish'
    );

    $the_query = new WP_Query($args);
    $have_posts = $the_query->have_posts();
    ?>
    <?php if ($have_posts) {
      ?>
      <div class="wp-collection-cards grid-cards">
        <?php while ($the_query->have_posts()) {
          $the_query->the_post();
          get_template_part("partials/post-card", '', array('post_id' => get_the_id()));
        }
        /* Restore original Post Data */
        wp_reset_postdata(); ?>
      </div>
      <?php \Theme\Helpers::get_paginate_links($the_query, $paged); ?>
    <?php } else {
      echo '<h3 class="wp-collection-no-posts headline-3 text-center">No Posts For This Tag</h3>';
    } ?>
  </div>
</section>
<?php get_footer(); ?>
