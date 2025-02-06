<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'services_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/services_block/screenshot.png" >';

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
  <div class="services-cards">
    <div class="services-card">
        <picture class="icon-wrapper">
          <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Layer_1-3.png" alt="">
        </picture>
        <div class="service-benefit-wrapper flex-col">
          <div class="title text-xl bold">Improved Online Visibility</div>
          <div class="text-md description regular gray-500"><p>Our tailored SEO strategies will help your website rank higher on search engines, making it easier for potential customers to find you.</p>
          </div>
        </div>
    </div>
    <div class="services-card">
      <picture class="icon-wrapper">
        <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Layer_1-3.png" alt="">
      </picture>
      <div class="service-benefit-wrapper flex-col">
        <div class="title text-xl bold">Improved Online Visibility</div>
        <div class="text-md description regular gray-500"><p>Our tailored SEO strategies will help your website rank higher on search engines, making it easier for potential customers to find you.</p>
        </div>
      </div>
    </div>
    <div class="services-card">
      <picture class="icon-wrapper">
        <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Layer_1-3.png" alt="">
      </picture>
      <div class="service-benefit-wrapper flex-col">
        <div class="title text-xl bold">Improved Online Visibility</div>
        <div class="text-md description regular gray-500"><p>Our tailored SEO strategies will help your website rank higher on search engines, making it easier for potential customers to find you.</p>
        </div>
      </div>
    </div>
    <div class="services-card">
      <picture class="icon-wrapper">
        <img decoding="async" src="http://localhost/threeSixty_theme/wp-content/uploads/2025/02/Layer_1-3.png" alt="">
      </picture>
      <div class="service-benefit-wrapper flex-col">
        <div class="title text-xl bold">Improved Online Visibility</div>
        <div class="text-md description regular gray-500"><p>Our tailored SEO strategies will help your website rank higher on search engines, making it easier for potential customers to find you.</p>
        </div>
      </div>
    </div>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
