<?php
// @author HOSSAM
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'faqs_block';
if (isset($block)) {
  $id = 'block_' . uniqid();
  if (!empty($block['anchor'])) {
    $id = $block['anchor'];
  }

// Create class attribute allowing for custom "className" and "align" values.
  if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
  }
  if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
  }
  if (get_field('is_screenshot')) :
    /* Render screenshot for example */
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/faqs_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$description = get_field('description');
$title = get_field('title');
$programmatic_or_manual = get_field("programmatic_or_manual");
if ($programmatic_or_manual === 'programmatic') {
  $query_options = get_field("query_options") ?: [];
  $number_of_posts = isset($query_options['number_of_posts']) ? (int)$query_options['number_of_posts'] : -1;
  $order = isset($query_options['order']) && in_array($query_options['order'], ['asc', 'desc']) ? $query_options['order'] : 'DESC';
  $args = [
    "post_type" => "faqs",
    "posts_per_page" => $number_of_posts,
    "order" => $order,
    "post_status" => "publish",
    "paged" => 1,
    'orderby' => 'date',
  ];
  $the_query = new WP_Query($args);
}
?>
<!-- region  Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <?php if ($title) { ?>
    <h1 class="title main-title"><?= $title ?></h1>
  <?php } ?>
  <?php if ($programmatic_or_manual === "manual") { ?>
    <div class="wrapper">
      <div class="accordion">
        <?php foreach (get_field("faqs_card") as $card):
          get_template_part("partials/faq-card", '', array('post_id' => $card));
        endforeach; ?>
      </div>
    </div>
  <?php } else { ?> <?php if (isset($the_query) && $the_query->have_posts()) { ?>
    <div class="wrapper">
      <div class="accordion">
        <?php while ($the_query->have_posts()) {
          $the_query->the_post();
          get_template_part("partials/faq-card", '', array('post_id' => get_the_ID()));
        } ?>
      </div>
    </div>
  <?php }
    /* Restore original Post Data */
    wp_reset_postdata(); ?>
  <?php } ?>
</div>
</section>
<!-- endregion swight_theme's Block -->
