<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'faq_block';
$has_border = get_field('has_border');
$has_border = $has_border ? ' block-has-border' : ' ';
if (isset($block)) {
  $id = 'block_' . uniqid();
  if (!empty($block['anchor'])) {
    $id = $block['anchor'];
  }
  $className .= $has_border;
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
$faqs_posts = get_field('faq_card');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="accordion">
    <div class="content flex-col gab-20">
      <?php if ($sub_title): ?>
        <div class="text-xl sub-title uppercase-text"><?= $sub_title ?></div>
      <?php endif; ?>
      <?php if ($title): ?>
        <div class="d-lg-h3 bold main-title title"><?= $title ?></div>
      <?php endif; ?>
      <?php if ($description): ?>
        <div class="text-xl description center-text"><?= $description ?></div>
      <?php endif; ?>
    </div>
    <?php if ($faqs_posts): ?>
      <?php foreach ($faqs_posts as $card):
        get_template_part("partials/faq-card", "", ["post_id" => $card->ID]);
      endforeach; ?>
    <?php endif; ?>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
