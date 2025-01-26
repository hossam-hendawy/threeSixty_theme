<?php
get_header();
?>
<?php if (have_posts()): the_post(); ?>
  <div class="single-post-wrapper">

    <!--     region breadcrumbs -->
    <div class="container">
      <?php if (function_exists('threeSixty_theme_breadcrumbs')) {
        threeSixty_theme_breadcrumbs();
      } ?>
    </div>
    <!--     endregion-->

    <div class="container">
      <div class="post-content-wrapper">
        <div class="blog-content flex-col">
          <div class="categories">
            <?php
            $categories = get_the_category();
            if ($categories) {
              foreach ($categories as $category) {
                $text_color = get_field('text_color', 'category_' . $category->term_id);
                $background_color = get_field('background_color', 'category_' . $category->term_id);
                $border_color = get_field('border_color', 'category_' . $category->term_id);

                // Set default colors if ACF fields are empty
                $text_color = $text_color ? esc_attr($text_color) : '#A15C07';
                $background_color = $background_color ? esc_attr($background_color) : '#FEFBE8';
                $border_color = $border_color ? esc_attr($border_color) : '#FDE172';

                echo '<div class="cat-name text-sm medium"
                    style="color: ' . $text_color . ';
                           background-color: ' . $background_color . ';
                           border-color: ' . $border_color . ';">
                    ' . esc_html($category->name) . '
                  </div>';
              }
            }
            ?>
          </div>
          <h1 class="post-title d-lg-h3 bold"><?php the_title(); ?></h1>
          <div class="excerpt text-xl gray-500">
            <?php echo get_the_excerpt(); ?>
          </div>
          <picture class="aspect-ratio feature-image">
            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="<?php the_title_attribute(); ?>">
          </picture>
          <div class="dynamic-content"  >
            <?php the_content(); ?>
          </div>

          <!--     add new code here -->

          <!--           tages -->


<!--           bootm content -->
<!-- authot -->

          <a href="#">f</a>
          <a href="#">x</a>
          <a href="#">ins</a>

        </div>


      </div>
    </div>

<!--     recent posts -->


  </div>
<?php endif; ?>
<?php
get_footer();
