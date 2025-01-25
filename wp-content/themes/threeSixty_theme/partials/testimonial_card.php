<?php
if (isset($args) && is_array($args)) {
  extract($args);
}
$post_id = $post_id ?? get_the_ID();
$comment = get_field('comment', $post_id);
$name = get_field('name', $post_id);
$job_title = get_field('job_title', $post_id);
$image = get_field('image', $post_id);
?>
<div class="swiper-slide testimonial-card">
  <?php if ($comment): ?>
    <div class="comment text-xl gray-500 medium"><?= $comment ?></div>
  <?php endif; ?>
  <div class="author-info">
    <?php if (!empty($image) && is_array($image)) { ?>
      <picture class="image-wrapper cover-image author-image">
        <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
      </picture>
    <?php } ?>
    <div class="about-author">
    <?php if ($name): ?>
      <div class="name text-lg semi-bold"><?= $name ?></div>
    <?php endif; ?>
    <?php if ($job_title): ?>
      <div class="jop-title captions regular text-md"><?= $job_title ?></div>
    <?php endif; ?>
    </div>
  </div>
</div>
