<?php
get_header();
global $post;
$post_id = get_the_ID();
?>
<?php if (have_posts()): the_post(); ?>
  <div class="single-package-wrapper">
    <?php the_content(); ?>
  </div>
<?php endif; ?>
<?php
get_footer();
