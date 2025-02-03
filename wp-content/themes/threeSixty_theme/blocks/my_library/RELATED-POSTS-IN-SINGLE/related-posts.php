<!--     related recent posts -->
<!-- please edit the code as you want -->

<section id="recent_posts" data-section-class="recent_posts" class="threeSixty_theme-block recent_posts">
  <div class="container">
    <h5 class="semi-bold recent-content-title">Related Posts</h5>
    <?php
    $current_post_id = get_the_ID();
    $args = [
      'post_type' => 'post',
      'posts_per_page' => -1,
      'post__not_in' => [$current_post_id],
      'orderby' => 'date',
      'order' => 'DESC',
    ];
    $query = new WP_Query($args);
    ?>
    <?php if ($query->have_posts()): ?>
      <div class="swiper recent-posts-swiper">
        <div class="swiper-wrapper">
          <?php while ($query->have_posts()): $query->the_post(); ?>
            <?php
            get_template_part("partials/recent-card", "", ["post_id" => get_the_ID()]);
            ?>
          <?php endwhile; ?>
        </div>
      </div>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
</section>
<!--  endregion-->
