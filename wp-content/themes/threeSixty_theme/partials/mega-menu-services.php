<?php
$post_id = @$args['post_id'] ?: get_the_ID();
$packages = @$args['packages'];
$services = @$args['services'];
$post_permalink = get_permalink($post_id);
?>
<?php if ($services === 'services-card') {
  $service_excerpt = get_field('service_excerpt', $post_id);
  $service_title = get_field('service_title', $post_id);
  $cta_icon = get_field('cta_icon', $post_id);
  $mega_menu_description = get_field('mega_menu_description', $post_id);
  ?>
  <a href="<?= $post_permalink ?>" class="package-box">
    <?php if (!empty($cta_icon) && is_array($cta_icon)) { ?>
      <picture class="package-icon cover-image" aria-hidden="true">
        <img src="<?= $cta_icon['url'] ?>" alt="<?= $cta_icon['alt'] ?>">
      </picture>
    <?php } ?>
    <div class="title-and-excerpt">
      <?php if ($service_title) { ?>
        <div class="package-title text-xl white-color"><?= $service_title ?></div>
      <?php } ?>
      <?php if ($mega_menu_description) { ?>
        <div class="package-description text-sm">
          <?= $mega_menu_description ?>
        </div>
      <?php } ?>
    </div>
  </a>

<?php } elseif ($packages === 'packages-card') {

  $package_title = get_field('package_title', $post_id);
  $package_excerpt = get_field('package_excerpt', $post_id);
  $package_price = get_field('package_price', $post_id);
  $package_icon = get_field('package_icon', $post_id);
  $package_includes_icon = get_field('package_includes_icon', $post_id);
  $mega_menu_description = get_field('mega_menu_description', $post_id);
  ?>
  <a href="<?= $post_permalink ?>" class="package-box">
    <?php if (!empty($package_icon) && is_array($package_icon)) { ?>
      <picture class="package-icon cover-image" aria-hidden="true">
        <img src="<?= $package_icon['url'] ?>" alt="<?= $package_icon['alt'] ?>">
      </picture>
    <?php } ?>
    <div class="title-and-excerpt">
      <?php if ($package_title) { ?>
        <div class="package-title text-xl white-color"><?= $package_title ?></div>
      <?php } ?>
      <?php if ($mega_menu_description) { ?>
        <div class="package-description text-sm">
          <?= $mega_menu_description ?>
        </div>
      <?php } ?>
    </div>
  </a>
<?php } ?>
