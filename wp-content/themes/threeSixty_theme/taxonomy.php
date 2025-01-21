<?php
/**
 * A Simple Category Template
 */
$tax = get_queried_object();
get_header(); ?>
<section class="wp_collection_block"
         data-section-class="wp_collection_block">
  <div class="container">
    <h2 class="wp-collection-title headline-1"><?= single_term_title(); ?></h2>
    <?php if ( term_description() ) { ?>
      <div class="wp-collection-description paragraph"><?= term_description() ?></div>
    <?php } ?>
    <?php
    $paged      = ( get_query_var( 'page_val' ) ? get_query_var( 'page_val' ) : 1 );
    $args       = array(
      'paged'          => $paged,
      'posts_per_page' => 13,
      'order'          => 'DESC',
      'orderby'        => 'date',
      'post_status'    => 'publish',
      'tax_query'      => array(
        array(
          'taxonomy' => $tax->taxonomy,
          'field'    => 'slug',
          'terms'    => $tax->slug
        )
      )
    );
    $the_query  = new WP_Query( $args );
    $have_posts = $the_query->have_posts();
    ?>
    <?php if ( $have_posts ) {
      ?>
      <div class="wp-collection-cards">
        <?php while ( $the_query->have_posts() ) {
          $the_query->the_post();
          get_template_part( "partials/collection-card", '', array( 'post_id' => get_the_id() ) );
        }
        /* Restore original Post Data */
        wp_reset_postdata(); ?>
      </div>
      <?php \Theme\Helpers::get_paginate_links( $the_query, $paged ); ?>
    <?php } else {
      echo '<h3 class="wp-collection-no-posts headline-3 text-center">No Posts For This Category</h3>';
    } ?>
  </div>
  <?php get_footer(); ?>
