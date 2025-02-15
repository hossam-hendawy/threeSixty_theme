<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'packages_service_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/packages_service_block/screenshot.png" >';

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
if ($programmatic_or_manual === 'programmatic') {
  $query_options = get_field("query_options") ?: [];
  $number_of_posts = isset($query_options['number_of_posts']) ? (int)$query_options['number_of_posts'] : -1;
  $order = isset($query_options['order']) && in_array($query_options['order'], ['asc', 'desc']) ? $query_options['order'] : 'DESC';
  $args = [
    "post_type" => "packages",
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
  <div class="overview-content gab-20 flex-col">
    <?php if ($sub_title): ?>
      <h2 class="text-xl gold center-text sub-title"><?= $sub_title ?></h2>
    <?php endif; ?>
    <?php if ($title): ?>
      <h3 class="d-lg-h3 gray-950 bold center-text overview-title"><?= $title ?></h3>
    <?php endif; ?>
    <?php if ($description): ?>
      <div class="text-xl gray-500 center-text overview-description"><?= $description ?></div>
    <?php endif; ?>
  </div>
  <?php if (have_rows('service_benefits')) { ?>
    <div class="services-cards">
      <?php while (have_rows('service_benefits')) {
        the_row();
        $icon = get_sub_field('icon');
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        ?>
        <div class="services-card">
          <?php if (!empty($icon) && is_array($icon)) { ?>
            <picture class="icon-wrapper">
              <img src="<?= $icon['url'] ?>" alt="<?= $icon['alt'] ?>">
            </picture>
          <?php } ?>
          <div class="service-benefit-wrapper flex-col">
            <?php if ($title): ?>
              <div class="title text-xl bold"><?= $title ?></div>
            <?php endif; ?>
            <?php if ($description): ?>
              <div class="text-md description regular gray-500"><?= $description ?></div>
            <?php endif; ?>
          </div>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
  <?php if ($programmatic_or_manual === "manual") { ?>
    <div class="content-wrapper">
        <?php foreach (get_field("package_card") as $card):
          get_template_part("partials/package-card", "", ["post_id" => $card->ID]);
        endforeach; ?>
    </div>
  <?php } elseif (isset($the_query) && $the_query->have_posts()) { ?>
    <div class="content-wrapper">
        <?php while ($the_query->have_posts()) {
          $the_query->the_post();
          get_template_part("partials/package-card", "", ["post_id" => get_the_ID()]);
        } ?>
        <?php wp_reset_postdata(); ?>
    </div>
  <?php } ?>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
