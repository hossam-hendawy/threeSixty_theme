<?php
if (isset($args) && is_array($args)) {
  extract($args);
}
$post_id = $post_id ?? get_the_ID();
$post_title = get_the_title($post_id);
$post_permalink = get_permalink($post_id);
$post_excerpt = get_the_excerpt($post_id);
$thumbnail_id = get_post_thumbnail_id($post_id);
$thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
$thumbnail_alt = $thumbnail_alt ? esc_attr($thumbnail_alt) : esc_attr($post_title);
?>
<div class="swiper-slide recent-card">
  <a href="<?= $post_permalink ?>" target="_self">
    <?php
    $picture_class = 'aspect-ratio image-wrapper image-hover-effect featured-image';
    echo bis_get_attachment_picture(
      $thumbnail_id,
      [
        375 => [327, 205, 1],
        600 => [266, 166, 1],
        768 => [350, 219, 1],
        992 => [462, 289, 1],
        1024 => [312, 195, 1],
        1280 => [384, 240, 1],
        1440 => [384, 240, 1],
        1920 => [384, 240, 1]
      ],
      [
        'retina' => true, 'picture_class' => $picture_class,
        'alt' => esc_attr($thumbnail_alt)
      ]
    );
    ?>
  </a>
  <a href="<?= $post_permalink ?>" target="_self" class="post-title d-xs-6 semi-bold"><?= $post_title ?>
    <svg class="post-title-svg" width="24" height="28" viewBox="0 0 24 28" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M7 21L17 11M17 11H7M17 11V21" stroke="#CA8504" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </a>
  <?php if ($post_excerpt): ?>
    <span class="excerpt text-md gray-500"><?= esc_html($post_excerpt) ?></span>
  <?php endif; ?>
  <div class="categories">
    <?php
    $categories = get_the_category();
    if ($categories) {
      foreach ($categories as $category) {
        if ($category->slug === 'uncategorized') {
          continue;
        }
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
</div>
