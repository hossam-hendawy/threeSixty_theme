<?php
get_header();
?>
<?php if ( have_posts() ): the_post(); ?>
  <div class="single-post-wrapper">
    <div class="container">
      <div class="entry-content">
        <?php the_content(); ?>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php
get_footer();

