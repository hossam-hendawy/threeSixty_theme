<?php
require_once('../../../wp-load.php');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$posts_per_page = 3;
$offset = 2 + ($page - 1) * $posts_per_page;

$args = array(
  'post_type' => 'post',
  'posts_per_page' => $posts_per_page,
  'orderby' => 'date',
  'order' => 'DESC',
  'offset' => $offset
);

$total_posts = wp_count_posts()->publish;
$total_pages = ceil(($total_posts - 2) / $posts_per_page);

$query = new WP_Query($args);

$posts_html = "";

if ($query->have_posts()) :
  while ($query->have_posts()) : $query->the_post();
    ob_start(); ?>
    <div class="post-card horizontal-card">
      <a href="<?php the_permalink(); ?>" class="post-image-card">
        <picture class="post-image aspect-ratio">
          <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
        </picture>
      </a>
      <div class="post-content flex-col">
        <h3 class="text-sm semi-bold category"><?php the_category(', '); ?></h3>
        <a href="<?php the_permalink(); ?>" class="d-sm-h5 semi-bold post-title"><?php the_title(); ?></a>
        <div class="text-md regular gray-600"><?php echo wp_trim_words(get_the_content(), 20, '...'); ?></div>
        <div class="about-author">
          <picture class="image-author">
            <img src="<?php echo get_avatar_url(get_the_author_meta('ID')); ?>" alt="<?php the_author(); ?>">
          </picture>
          <div class="author-info">
            <h5 class="text-sm semi-bold author-name"><?php the_author(); ?></h5>
            <h6 class="text-sm gray-600 author-jop">Senior Marketing Consultant</h6>
          </div>
        </div>
      </div>
    </div>
    <?php
    $posts_html .= ob_get_clean();
  endwhile;
  wp_reset_postdata();
endif;

echo json_encode([
  "posts" => $posts_html,
  "totalPages" => $total_pages
]);

exit;
?>
