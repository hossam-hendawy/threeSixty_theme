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
    ob_start();
    get_template_part("partials/horizontal-post-card", "", ["post_id" => get_the_ID()]);
    ?>
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
<!--https://chatgpt.com/c/679f0a85-3ce0-8001-ba1e-f51cca9d0642-->
