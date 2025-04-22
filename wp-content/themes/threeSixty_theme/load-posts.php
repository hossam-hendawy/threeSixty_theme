<?php
require_once('../../../wp-load.php');

header('Content-Type: application/json; charset=UTF-8');
ob_clean();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$lang = $_GET['lang'] ?? 'en';

if (function_exists('do_action')) {
  do_action('wpml_switch_language', $lang);
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$posts_per_page = 3;
$offset = 2 + ($page - 1) * $posts_per_page;

$args = array(
  'post_type' => 'post',
  'posts_per_page' => $posts_per_page,
  'orderby' => 'date',
  'order' => 'DESC',
  'offset' => $offset,
  'lang' => $lang
);

$query = new WP_Query($args);
$total_posts = $query->found_posts;
$total_pages = ceil(($total_posts - 2) / $posts_per_page);

$posts_html = "";
if ($query->have_posts()) {
  while ($query->have_posts()) {
    $query->the_post();
    ob_start();
    get_template_part("partials/horizontal-post-card", "", ["post_id" => get_the_ID()]);
    $posts_html .= ob_get_clean();
  }
  wp_reset_postdata();
}

ob_end_clean();

echo json_encode([
  "posts" => html_entity_decode($posts_html, ENT_QUOTES, 'UTF-8'),
  "totalPages" => $total_pages
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);

exit;
