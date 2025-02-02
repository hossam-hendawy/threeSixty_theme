<?php
if (isset($args) && is_array($args)) {
  extract($args);
}
$post_id = $post_id ?? get_the_ID();
$post_title = get_the_title($post_id);
$post_permalink = get_permalink($post_id);
$thumbnail_id = get_post_thumbnail_id($post_id);
$thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
$thumbnail_alt = $thumbnail_alt ? esc_attr($thumbnail_alt) : esc_attr($post_title);
?>
<div class="post-card">
  <a href="<?= $post_permalink ?>" class="post-image-card" target="_self">
    <?php
    $picture_class = 'post-image aspect-ratio';
    echo bis_get_attachment_picture(
      $thumbnail_id,
      [
        375 => [327, 196, 1],
        600 => [552, 331, 1],
        768 => [344, 206, 1],
        992 => [444, 266, 1],
        1024 => [461, 276, 1],
        1280 => [580, 348, 1],
        1440 => [580, 348, 1],
        1920 => [580, 348, 1]
      ],
      [
        'retina' => true, 'picture_class' => $picture_class,
        'alt' => esc_attr($thumbnail_alt)
      ]
    );
    ?>
  </a>
  <div class="post-content flex-col">
    <div class="text-sm semi-bold category"><?php the_category(', '); ?></div>
    <a href="<?= $post_permalink ?>" class="d-sm-h5 semi-bold post-title"><?= $post_title ?></a>
    <div class="text-md regular gray-600 post-excerpt">
      <?php echo has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20, '...'); ?>
    </div>
    <div class="about-author">
      <picture class="image-author">
        <img src="<?php echo get_avatar_url(get_the_author_meta('ID')); ?>" alt="<?php the_author(); ?>">
      </picture>
      <div class="author-info">
        <h5 class="text-sm semi-bold author-name"><?php the_author(); ?></h5>
        <h6 class="text-sm gray-600 author-jop">Senior Marketing
          Consultant</h6>
      </div>
    </div>
  </div>
</div>
