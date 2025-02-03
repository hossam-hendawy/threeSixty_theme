<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (function_exists('icl_get_current_language')) {
  $current_lang = icl_get_current_language(); // للحصول على اللغة الحالية من WPML
} else {
  $current_lang = 'ar'; // لو WPML مش شغال، خلي الافتراضي عربي
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$posts_per_page = 3;
$offset = 2 + ($page - 1) * $posts_per_page;

$args = array(
  'post_type'      => 'post',
  'posts_per_page' => $posts_per_page,
  'orderby'        => 'date',
  'order'          => 'DESC',
  'offset'         => $offset,
  'lang'           => $current_lang // ⬅️ هنا الفلترة حسب اللغة
);

$total_posts = wp_count_posts()->publish;
$total_pages = ceil(($total_posts - 2) / $posts_per_page);

$query = new WP_Query($args);
$posts_html = "";

if ($query->have_posts()) :
  while ($query->have_posts()) : $query->the_post();
    ob_start();
    get_template_part("partials/horizontal-post-card", "", ["post_id" => get_the_ID()]);
    $posts_html .= ob_get_clean();
  endwhile;
  wp_reset_postdata();
endif;

wp_send_json([
  "posts" => $posts_html,
  "totalPages" => $total_pages
]);
exit;
?>
