<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'faq_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/faq_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
$programmatic_or_manual = get_field("programmatic_or_manual");
$faqs_posts = get_field('faq_card');
if ($programmatic_or_manual === 'programmatic') {
  $query_options = get_field("query_options") ?: [];
  $number_of_posts = isset($query_options['number_of_posts']) ? (int)$query_options['number_of_posts'] : 3;
  if ($number_of_posts > 3) {
    $number_of_posts = 3;
  }
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
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <?php if ($programmatic_or_manual === "manual") { ?>

      <div class="accordion">
        <div class="left-content flex-col gab-20">
          <?php if ($sub_title): ?>
            <h1 class="text-xl sub-title"><?= $sub_title ?></h1>
          <?php endif; ?>
          <?php if ($title): ?>
            <h3 class="bold title"><?= $title ?></h3>
          <?php endif; ?>
          <?php if ($description): ?>
            <div class="text-lg description"><?= $description ?></div>
          <?php endif; ?>
        </div>

        <?php foreach (get_field("faq_card") as $card):
          get_template_part("partials/faq-card", "", ["post_id" => $card->ID]);
        endforeach; ?>
      </div>
  <?php } elseif (isset($the_query) && $the_query->have_posts()) { ?>
  <div class="accordion">
    <div class="left-content flex-col gab-20">
      <?php if ($sub_title): ?>
        <h1 class="text-xl sub-title"><?= $sub_title ?></h1>
      <?php endif; ?>
      <?php if ($title): ?>
        <h3 class="bold title"><?= $title ?></h3>
      <?php endif; ?>
      <?php if ($description): ?>
        <div class="text-lg description"><?= $description ?></div>
      <?php endif; ?>
    </div>
    <?php while ($the_query->have_posts()) {
          $the_query->the_post();
          get_template_part("partials/faq-card", "", ["post_id" => get_the_ID()]);
        } ?>
        <?php wp_reset_postdata(); ?>
  </div>
  <?php } ?>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
