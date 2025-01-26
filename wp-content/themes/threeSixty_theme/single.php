<?php
get_header();
global $post;
$post_id = get_the_ID();
$author_id = $post->post_author;
$author_name = get_the_author_meta('display_name', $author_id);
$author_jop = get_the_author_meta('display_name', $author_id);
$author_image = get_the_author_meta('display_name', $author_id);
$current_user_id = get_current_user_id();
$user_image = get_field('user_image', 'user_' . $current_user_id);
$user_jop_title = get_field('user_jop_title', 'user_' . $current_user_id);
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
            <?php
            $tags = get_the_tags();
            if (has_tag()) {
              echo '<div class=tags-wrapper>';
              $post_tags = get_the_tags();
              foreach ($post_tags as $tag) {
                $tag_link = get_tag_link($tag->term_id);
                echo '<a  href="' . esc_url($tag_link) . '" class="tag-link text-sm gray-600 medium ">' . esc_html($tag->name) . '</a> ';
              }
              echo '</div>';
            }
            ?>
<!--           bootm content -->
<!-- authot -->
          <div class="author-social-links">
            <div class="about-author">

              <?php if (!empty($user_image) && is_array($user_image)) { ?>
                <picture class="image-author">
                  <img src="<?= $user_image['url'] ?>" alt="<?= $user_image['alt'] ?>">
                </picture>
              <?php } ?>
              <div class="author-info">
                <h5 class="text-sm semi-bold author-name"><?= $author_name ?></h5>
                <h6 class="text-sm gray-600 author-jop"> <?= $user_jop_title ?></h6>
              </div>
            </div>
            <div class="social-links">
              <a class="social-link text-sm semi-bold copy-link" href="#">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0_273_4042)">
                    <path d="M4.16699 12.4998C3.39042 12.4998 3.00214 12.4998 2.69585 12.373C2.28747 12.2038 1.96302 11.8794 1.79386 11.471C1.66699 11.1647 1.66699 10.7764 1.66699 9.99984V4.33317C1.66699 3.39975 1.66699 2.93304 1.84865 2.57652C2.00844 2.26292 2.2634 2.00795 2.57701 1.84816C2.93353 1.6665 3.40024 1.6665 4.33366 1.6665H10.0003C10.7769 1.6665 11.1652 1.6665 11.4715 1.79337C11.8798 1.96253 12.2043 2.28698 12.3735 2.69536C12.5003 3.00165 12.5003 3.38993 12.5003 4.1665M10.167 18.3332H15.667C16.6004 18.3332 17.0671 18.3332 17.4236 18.1515C17.7372 17.9917 17.9922 17.7368 18.152 17.4232C18.3337 17.0666 18.3337 16.5999 18.3337 15.6665V10.1665C18.3337 9.23308 18.3337 8.76637 18.152 8.40985C17.9922 8.09625 17.7372 7.84128 17.4236 7.68149C17.0671 7.49984 16.6004 7.49984 15.667 7.49984H10.167C9.23357 7.49984 8.76686 7.49984 8.41034 7.68149C8.09674 7.84128 7.84177 8.09625 7.68198 8.40985C7.50033 8.76637 7.50033 9.23308 7.50033 10.1665V15.6665C7.50033 16.5999 7.50033 17.0666 7.68198 17.4232C7.84177 17.7368 8.09674 17.9917 8.41034 18.1515C8.76686 18.3332 9.23357 18.3332 10.167 18.3332Z" stroke="#364152" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                  </g>
                  <defs>
                    <clipPath id="clip0_273_4042">
                      <rect width="20" height="20" fill="white"/>
                    </clipPath>
                  </defs>
                </svg>
                Copy link
              </a>
              <a class="social-link" href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0_273_4048)">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.2879 19.1668L8.66337 12.5752L2.87405 19.1668H0.424805L7.57674 11.0261L0.424805 0.833496H6.71309L11.0717 7.04601L16.5327 0.833496H18.982L12.1619 8.59723L19.5762 19.1668H13.2879ZM16.0154 17.3085H14.3665L3.93176 2.69183H5.58092L9.7601 8.54446L10.4828 9.56005L16.0154 17.3085Z" fill="#9AA3B2"/>
                  </g>
                  <defs>
                    <clipPath id="clip0_273_4048">
                      <rect width="20" height="20" fill="white"/>
                    </clipPath>
                  </defs>
                </svg>
              </a>
              <a class="social-link" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0_273_4051)">
                    <path d="M20 10C20 4.47715 15.5229 0 10 0C4.47715 0 0 4.47715 0 10C0 14.9912 3.65684 19.1283 8.4375 19.8785V12.8906H5.89844V10H8.4375V7.79688C8.4375 5.29063 9.93047 3.90625 12.2146 3.90625C13.3084 3.90625 14.4531 4.10156 14.4531 4.10156V6.5625H13.1922C11.95 6.5625 11.5625 7.3334 11.5625 8.125V10H14.3359L13.8926 12.8906H11.5625V19.8785C16.3432 19.1283 20 14.9912 20 10Z" fill="#9AA3B2"/>
                    <path d="M13.8926 12.8906L14.3359 10H11.5625V8.125C11.5625 7.33418 11.95 6.5625 13.1922 6.5625H14.4531V4.10156C14.4531 4.10156 13.3088 3.90625 12.2146 3.90625C9.93047 3.90625 8.4375 5.29063 8.4375 7.79688V10H5.89844V12.8906H8.4375V19.8785C9.47287 20.0405 10.5271 20.0405 11.5625 19.8785V12.8906H13.8926Z" fill="white"/>
                  </g>
                  <defs>
                    <clipPath id="clip0_273_4051">
                      <rect width="20" height="20" fill="white"/>
                    </clipPath>
                  </defs>
                </svg>
              </a>
              <a class="social-link" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode(get_the_title()); ?>" target="_blank">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0_273_4055)">
                    <path d="M18.5236 0H1.47639C1.08483 0 0.709301 0.155548 0.432425 0.432425C0.155548 0.709301 0 1.08483 0 1.47639V18.5236C0 18.9152 0.155548 19.2907 0.432425 19.5676C0.709301 19.8445 1.08483 20 1.47639 20H18.5236C18.9152 20 19.2907 19.8445 19.5676 19.5676C19.8445 19.2907 20 18.9152 20 18.5236V1.47639C20 1.08483 19.8445 0.709301 19.5676 0.432425C19.2907 0.155548 18.9152 0 18.5236 0ZM5.96111 17.0375H2.95417V7.48611H5.96111V17.0375ZM4.45556 6.1625C4.11447 6.16058 3.7816 6.05766 3.49895 5.86674C3.21629 5.67582 2.99653 5.40544 2.8674 5.08974C2.73826 4.77404 2.70554 4.42716 2.77336 4.09288C2.84118 3.7586 3.0065 3.4519 3.24846 3.21148C3.49042 2.97107 3.79818 2.80772 4.13289 2.74205C4.4676 2.67638 4.81426 2.71133 5.12913 2.84249C5.44399 2.97365 5.71295 3.19514 5.90205 3.47901C6.09116 3.76288 6.19194 4.09641 6.19167 4.4375C6.19488 4.66586 6.15209 4.89253 6.06584 5.104C5.97959 5.31547 5.85165 5.50742 5.68964 5.66839C5.52763 5.82936 5.33487 5.95607 5.12285 6.04096C4.91083 6.12585 4.68389 6.16718 4.45556 6.1625ZM17.0444 17.0458H14.0389V11.8278C14.0389 10.2889 13.3847 9.81389 12.5403 9.81389C11.6486 9.81389 10.7736 10.4861 10.7736 11.8667V17.0458H7.76667V7.49306H10.6583V8.81667H10.6972C10.9875 8.22917 12.0042 7.225 13.5556 7.225C15.2333 7.225 17.0458 8.22083 17.0458 11.1375L17.0444 17.0458Z" fill="#9AA3B2"/>
                  </g>
                  <defs>
                    <clipPath id="clip0_273_4055">
                      <rect width="20" height="20" fill="white"/>
                    </clipPath>
                  </defs>
                </svg>

              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
<!--     recent posts -->
    <section id="recent_posts" data-section-class="recent_posts" class="threeSixty_theme-block recent_posts js-loaded">
      <div class="container">
        <h5 class="semi-bold recent-content-title">Related Posts</h5>
        <?php
        $current_post_id = get_the_ID();
        $args = [
          'post_type' => 'post',
          'posts_per_page' => 3,
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
        <div class="swiper-navigations">
          <div class="swiper-button-prev swiper-navigation arrow animation-scale-me" role="button" tabindex="0" aria-label="Previous Slide">
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
              <path d="M0.5 0.5H55.5V55.5H0.5V0.5Z" stroke="#475467"/>
              <path d="M35 28H21M21 28L28 35M21 28L28 21" stroke="#475467" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="swiper-button-next swiper-navigation arrow animation-scale-me" role="button" tabindex="0" aria-label="Next Slide">
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
              <path d="M0.5 0.5H55.5V55.5H0.5V0.5Z" stroke="#98A2B3"/>
              <path d="M21 28H35M35 28L28 21M35 28L28 35" stroke="#475467" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>
      </div>
    </section>

  </div>
<?php endif; ?>
<?php
get_footer();
