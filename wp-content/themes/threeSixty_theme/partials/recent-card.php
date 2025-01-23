<?php
if (isset($args) && is_array($args)) {
  extract($args);
}
$post_id = $post_id ?? get_the_ID();
$post_title = get_the_title($post_id);
$post_permalink = get_permalink($post_id);
$post_excerpt = get_the_excerpt($post_id);
$thumbnail_id = get_post_thumbnail_id($post_id);
?>
<div class="swiper-slide recent-card">
  <a href="<?= $post_permalink ?>" target="_self">
    <?php
    $picture_class = 'aspect-ratio image-wrapper image-hover-effect featured-image';
    echo bis_get_attachment_picture(
      $thumbnail_id,
      [
        375 => [335, 206, 1],
        1024 => [305, 187, 1],
        1280 => [365, 224, 1],
        1440 => [418, 257, 1],
        1920 => [418, 257, 1],
        2500 => [418, 257, 1]
      ],
      [
        'retina' => true, 'picture_class' => $picture_class,
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
</div>
