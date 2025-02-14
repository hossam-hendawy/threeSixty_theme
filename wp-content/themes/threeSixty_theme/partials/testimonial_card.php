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
  <svg class="svg-quote" width="64" height="42" viewBox="0 0 64 42" fill="none" aria-hidden="true">
    <g clip-path="url(#svg-quote)">
      <path d="M64 0.199951H47.986L32.7086 15.6983V41.7999H58.4191V15.6983H48.7226L64 0.199951Z" fill="#D0D5DD"/>
      <path d="M15.2774 0.199951L0 15.6983V41.7999H25.7025V15.6983H16.014L31.2914 0.199951H15.2774Z" fill="#D0D5DD"/>
    <defs>
      <clipPath id="svg-quote">
        <rect width="64" height="41.6" fill="white" transform="translate(0 0.199951)"/>
      </clipPath>
    </defs>
  </svg>
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
