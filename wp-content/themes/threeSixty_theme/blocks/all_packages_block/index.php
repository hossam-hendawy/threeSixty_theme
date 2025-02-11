<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'all_packages_block';
if (isset($block)) {
  $id = 'block_' . uniqid();
  if (!empty($block['anchor'])) {
    $id = $block['anchor'];
  }

// Create class attribute allowing for custom "className" and "align" values.
  if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
  }
  if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
  }
  if (get_field('is_screenshot')) :
    /* Render screenshot for example */
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/all_packages_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$title = get_field('title');
$description = get_field('description');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <?php if (have_rows('service_benefits')) { ?>
    <div class="services-cards">
      <?php while (have_rows('service_benefits')) {
        the_row();
        $icon = get_sub_field('icon');
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        ?>
        <div class="services-card">
          <?php if (!empty($icon) && is_array($icon)) { ?>
            <picture class="icon-wrapper">
              <img src="<?= $icon['url'] ?>" alt="<?= $icon['alt'] ?>">
            </picture>
          <?php } ?>
          <div class="service-benefit-wrapper flex-col">
            <?php if ($title): ?>
              <div class="title text-xl bold"><?= $title ?></div>
            <?php endif; ?>
            <?php if ($description): ?>
              <div class="text-md description regular gray-500"><?= $description ?></div>
            <?php endif; ?>
          </div>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
  <div class="content-wrapper">
    <div class="package-box-wrapper">
      <div class="package-title-and-price">
        <div class="icon-and-package-title">
          <picture class="package-icon-wrapper">
            <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Isolation_Mode-1.svg" alt="">
          </picture>
          <div class="title-and-excerpt flex-col">
            <div class="package-title d-xs-6 bold uppercase-text"><p>STARTER
                <strong>PACKAGE</strong></p>
            </div>
            <div class="text-md description regular gray-500">The Perfect
              Beginning for Your Project.
            </div>
          </div>
        </div>
        <div class="price-container">
          <sub class="d-md-h4 semi-bold gray-600">$</sub>
          <div class="price d-lg-h3 bold gray-600">1590</div>
        </div>
      </div>
      <div class="package-includes">
        <div class="package-includes-title text-md semi-bold">This package
          includes:
        </div>
        <div class="package-includes-wrapper">
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Simple Website Design</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Social Media Management</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Content Creation</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Basic SEO Optimization</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Google Analytics Setup</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Social Media Ad Design</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Email business Setup</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Contact Form Integration</div>
          </div>
        </div>
      </div>
      <div class="cta-button-wrapper">
        <a class="theme-cta-button uppercase-text" href="#" target="">
          GET STARTED
          <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
            <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"></path>
            <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"></path>
          </svg>
        </a>
      </div>
    </div>
    <div class="package-box-wrapper">
      <div class="package-title-and-price">
        <div class="icon-and-package-title">
          <picture class="package-icon-wrapper">
            <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Isolation_Mode-1.png" alt="">
          </picture>
          <div class="title-and-excerpt flex-col">
            <div class="package-title d-xs-6 bold uppercase-text"><p>STARTER
                <strong>PACKAGE</strong></p>
            </div>
            <div class="text-md description regular gray-500">The Perfect
              Beginning for Your Project.
            </div>
          </div>
        </div>
        <div class="price-container">
          <sub class="d-md-h4 semi-bold gray-600">$</sub>
          <div class="price d-lg-h3 bold gray-600">1590</div>
        </div>
      </div>
      <div class="package-includes">
        <div class="package-includes-title text-md semi-bold">This package
          includes:
        </div>
        <div class="package-includes-wrapper">
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Simple Website Design</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Social Media Management</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Content Creation</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Basic SEO Optimization</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Google Analytics Setup</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Social Media Ad Design</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Email business Setup</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Contact Form Integration</div>
          </div>
        </div>
      </div>
      <div class="cta-button-wrapper">
        <a class="theme-cta-button uppercase-text" href="#" target="">
          GET STARTED
          <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
            <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"></path>
            <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"></path>
          </svg>
        </a>
      </div>
    </div>
    <div class="package-box-wrapper">
      <div class="package-title-and-price">
        <div class="icon-and-package-title">
          <picture class="package-icon-wrapper">
            <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Isolation_Mode-1.png" alt="">
          </picture>
          <div class="title-and-excerpt flex-col">
            <div class="package-title d-xs-6 bold uppercase-text"><p>STARTER
                <strong>PACKAGE</strong></p>
            </div>
            <div class="text-md description regular gray-500">The Perfect
              Beginning for Your Project.
            </div>
          </div>
        </div>
        <div class="price-container">
          <sub class="d-md-h4 semi-bold gray-600">$</sub>
          <div class="price d-lg-h3 bold gray-600">1590</div>
        </div>
      </div>
      <div class="package-includes">
        <div class="package-includes-title text-md semi-bold">This package
          includes:
        </div>
        <div class="package-includes-wrapper">
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Simple Website Design</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Social Media Management</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Content Creation</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Basic SEO Optimization</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Google Analytics Setup</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Social Media Ad Design</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Email business Setup</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Contact Form Integration</div>
          </div>
        </div>
      </div>
      <div class="cta-button-wrapper">
        <a class="theme-cta-button uppercase-text" href="#" target="">
          GET STARTED
          <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
            <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"></path>
            <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"></path>
          </svg>
        </a>
      </div>
    </div>
    <div class="package-box-wrapper">
      <div class="package-title-and-price">
        <div class="icon-and-package-title">
          <picture class="package-icon-wrapper">
            <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Isolation_Mode-1.png" alt="">
          </picture>
          <div class="title-and-excerpt flex-col">
            <div class="package-title d-xs-6 bold uppercase-text"><p>STARTER
                <strong>PACKAGE</strong></p>
            </div>
            <div class="text-md description regular gray-500">The Perfect
              Beginning for Your Project.
            </div>
          </div>
        </div>
        <div class="price-container">
          <sub class="d-md-h4 semi-bold gray-600">$</sub>
          <div class="price d-lg-h3 bold gray-600">1590</div>
        </div>
      </div>
      <div class="package-includes">
        <div class="package-includes-title text-md semi-bold">This package
          includes:
        </div>
        <div class="package-includes-wrapper">
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Simple Website Design</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Social Media Management</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Content Creation</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Basic SEO Optimization</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Google Analytics Setup</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Social Media Ad Design</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Email business Setup</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Contact Form Integration</div>
          </div>
        </div>
      </div>
      <div class="cta-button-wrapper">
        <a class="theme-cta-button uppercase-text" href="#" target="">
          GET STARTED
          <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
            <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"></path>
            <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"></path>
          </svg>
        </a>
      </div>
    </div>
    <div class="package-box-wrapper">
      <div class="package-title-and-price">
        <div class="icon-and-package-title">
          <picture class="package-icon-wrapper">
            <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Isolation_Mode-1.png" alt="">
          </picture>
          <div class="title-and-excerpt flex-col">
            <div class="package-title d-xs-6 bold uppercase-text"><p>STARTER
                <strong>PACKAGE</strong></p>
            </div>
            <div class="text-md description regular gray-500">The Perfect
              Beginning for Your Project.
            </div>
          </div>
        </div>
        <div class="price-container">
          <sub class="d-md-h4 semi-bold gray-600">$</sub>
          <div class="price d-lg-h3 bold gray-600">1590</div>
        </div>
      </div>
      <div class="package-includes">
        <div class="package-includes-title text-md semi-bold">This package
          includes:
        </div>
        <div class="package-includes-wrapper">
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Simple Website Design</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Social Media Management</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Content Creation</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Basic SEO Optimization</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Google Analytics Setup</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Social Media Ad Design</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Email business Setup</div>
          </div>
          <div class="text">
            <picture class="icon">
              <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Check-icon.png" alt="">
            </picture>
            <div class="the-text text-md medium">Contact Form Integration</div>
          </div>
        </div>
      </div>
      <div class="cta-button-wrapper">
        <a class="theme-cta-button uppercase-text" href="#" target="">
          GET STARTED
          <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
            <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"></path>
            <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"></path>
          </svg>
        </a>
      </div>
    </div>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
