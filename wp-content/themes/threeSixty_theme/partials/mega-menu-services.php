<?php
$post_id = @$args['post_id'] ?: get_the_ID();
$post_permalink = get_permalink($post_id);
$service_excerpt = get_field('service_excerpt', $post_id);
$service_title = get_field('service_title', $post_id);
$cta_icon = get_field('cta_icon', $post_id);
?>
<a href="<?= $post_permalink ?>" class="package-box">
  <?php if (!empty($cta_icon) && is_array($cta_icon)) { ?>
    <picture class="package-icon cover-image" aria-hidden="true">
      <img src="<?= $cta_icon['url'] ?>" alt="<?= $cta_icon['alt'] ?>">
    </picture>
  <?php } ?>
  <div class="title-and-excerpt">
    <?php if ($service_title) { ?>
      <div  class="package-title text-xl white-color"><?= $service_title ?></div>
    <?php } ?>
    <?php if ($service_excerpt) { ?>
      <div class="package-description text-sm">
        <?= $service_excerpt ?>
      </div>
    <?php } ?>
  </div>
</a>
