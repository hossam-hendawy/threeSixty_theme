<?php
$post_id = @$args['post_id'] ?: get_the_ID();
$post_permalink = get_permalink($post_id);
$service_image = get_field('service_image', $post_id);
$service_excerpt = get_field('service_excerpt', $post_id);
$service_title = get_field('service_title', $post_id);
$cta_icon = get_field('cta_icon', $post_id);
?>
<div class="offering-card">
  <div class="image-title flex-col">
    <?php if (!empty($service_image) && is_array($service_image)) { ?>
      <picture class="offering-image image-wrapper cover-image ">
        <img src="<?= $service_image['url'] ?>" alt="<?= $service_image['alt'] ?>">
      </picture>
    <?php } ?>
    <?php if ($service_title) { ?>
      <div class="center-text offering-title d-xs-6"><?= $service_title ?></div>
    <?php } ?>
  </div>
  <div class="description-btn flex-col">
    <?php if ($service_excerpt) { ?>
      <div class="description text-md regular center-text">
        <?= $service_excerpt ?>
      </div>
    <?php } ?>
    <a class="theme-cta-button offering-btn btn-white" href="<?= $post_permalink ?>" target="_self">
      Explore More
      <?php if (!empty($cta_icon) && is_array($cta_icon)) { ?>
        <picture class="icon" aria-hidden="true">
          <img src="<?= $cta_icon['url'] ?>" alt="<?= $cta_icon['alt'] ?>">
        </picture>
      <?php } ?>

    </a>
  </div>
</div>
