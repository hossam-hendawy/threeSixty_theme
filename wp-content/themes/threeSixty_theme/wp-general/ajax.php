<?php

add_action('wp_ajax_more_posts', 'more_posts');
add_action('wp_ajax_nopriv_more_posts', 'more_posts');
function more_posts()
{

  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce(sanitize_key($_POST['_ajax_nonce']), 'nonce_ajax_more_posts')) {
    return wp_send_json_error(esc_html__('Number not only once is invalid', 'threeSixty_theme'), 404);
  }


  $args = json_decode(stripcslashes(trim($_POST['args'], '"')), true);
  $template = $_POST['template'];

  $posts_query = new WP_Query($args);
  header('X-WP-arg-pages: ' . ($args['paged'] ?: 1));
  header('X-WP-Has-More-Pages: ' . ($posts_query->max_num_pages - ($args['paged'] ?: 1) > 0));
  header('X-WP-Total-Pages: ' . ($posts_query->max_num_pages));
//var_dump($args);
//return
  ob_start();
  while ($posts_query->have_posts()):$posts_query->the_post();
    get_template_part($template);
  endwhile;
  $posts_out = ob_get_clean();
  wp_reset_postdata();

  wp_send_json_success($posts_out, 200);


}


