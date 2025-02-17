<?php
$post_id = @$args['post_id'] ?: get_the_ID();
$post_permalink = get_permalink($post_id);
$package_title = get_field('package_title', $post_id);
$package_excerpt = get_field('package_excerpt', $post_id);
$package_price = get_field('package_price', $post_id);
$package_icon = get_field('package_icon', $post_id);
$package_includes_icon = get_field('package_includes_icon', $post_id);
?>
<?php if ($package_price) { ?>
  <div class="package-box-wrapper">
    <div class="package-title-and-price">
      <div class="icon-and-package-title">
        <?php if (!empty($package_icon) && is_array($package_icon)) { ?>
          <picture class="package-icon-wrapper">
            <img src="<?= $package_icon['url'] ?>" alt="<?= $package_icon['alt'] ?>">
          </picture>
        <?php } ?>
        <div class="title-and-excerpt flex-col">
          <?php if ($package_title): ?>
            <div class="package-title d-xs-6 bold uppercase-text"><?= $package_title ?></div>
          <?php endif; ?>
          <?php if ($package_excerpt): ?>
            <div class="text-md description regular gray-500"><?= $package_excerpt ?></div>
          <?php endif; ?>
        </div>
      </div>
      <?php if ($package_price): ?>
        <div class="price-container">
          <sub class="d-md-h4 semi-bold gray-600">$</sub>
          <div class="price d-lg-h3 bold gray-600"><?= $package_price ?></div>
        </div>
      <?php endif; ?>
    </div>
    <div class="package-includes">
      <div class="package-includes-title text-md semi-bold">This
        package includes:</div>
      <div class="package-includes-wrapper">
        <?php
        if (have_rows('package_includes', $post_id)) {
          $count = 0;
          ?>
          <?php while (have_rows('package_includes', $post_id)) {
            the_row();
            $text = get_sub_field('text');
            $count++;
            if ($count > 8) {
              break;
            }
            ?>
            <div class="text">
              <?php if (!empty($package_includes_icon) && is_array($package_includes_icon)) { ?>
                <picture class="icon">
                  <img src="<?= $package_includes_icon['url'] ?>" alt="<?= $package_includes_icon['alt'] ?>">
                </picture>
              <?php } ?>
              <div class="the-text text-md medium"><?= $text ?></div>
            </div>
          <?php } ?>
        <?php } ?>
      </div>
    </div>
    <div class="cta-button-wrapper">
      <a class="theme-cta-button" href="<?= $post_permalink ?>" target="_self">
        Explore More
        <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
          <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"/>
          <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"/>
        </svg>
      </a>
    </div>
  </div>
<?php } ?>
