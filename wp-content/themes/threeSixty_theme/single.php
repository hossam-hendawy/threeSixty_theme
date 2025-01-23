<?php
get_header();
?>
<?php if ( have_posts() ): the_post(); ?>
  <div class="single-post-wrapper">
    <div class="container">
      <div class="entry-content">
        <?php the_content(); ?>
        <div class="blog-content flex-col ">
          <div class="buttons">
            <div class="button text-sm medium yellow">Markting</div>
            <div class="button text-sm medium Purple">Markting</div>
            <div class="button text-sm medium Pink">Markting</div>
          </div>
          <h1 class="post-title d-lg-h3 bold"><?php the_title(); ?></h1>

          <div class="excerpt text-xl gray-500">
            <?php echo get_the_excerpt(); ?>
          </div>

          <picture class="aspect-ratio feature-image">
            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="<?php the_title_attribute(); ?>">
          </picture>

        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php
get_footer();

