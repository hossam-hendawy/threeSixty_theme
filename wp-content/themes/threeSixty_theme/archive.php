<?php
get_header();
// get categories
$cat_args   = array(
  'taxonomy' => 'category',
  'orderby'  => 'name',
  'order'    => 'ASC',
);
$categories = get_categories( $cat_args );
?>
  <section class="wp_collection_block"
           data-section-class="wp_collection_block">
    <div class="container">
      <h2 class="wp-collection-title headline-1"><?= get_the_archive_title(); ?></h2>
      <?php if ( get_the_archive_description() ) { ?>
        <div class="wp-collection-description paragraph"><?= get_the_archive_description() ?></div>
      <?php } ?>
      <?php foreach ( $categories as $category ) {
        $args  = array(
          'showposts'           => 5,
          'category__in'        => array( $category->term_id ),
          'orderby'             => 'date',
          'order'               => 'DESC',
          'ignore_sticky_posts' => 1
        );
        $posts = get_posts( $args );
        if ( $posts ) {
          foreach ( $posts as $post ) {
            $post_id = $post->ID;
            get_template_part( "partials/collection-card", '', array( 'post_id' => $post_id ) );
          }
        }
      } ?>
    </div>
  </section>
<?php get_footer();
