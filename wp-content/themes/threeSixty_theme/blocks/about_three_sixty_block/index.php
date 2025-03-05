<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'about_three_sixty_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/about_three_sixty_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
$image = get_field('image');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="cards-wrapper">
  <div class="left-content flex-col gab-20">
    <?php if ($sub_title): ?>
      <h2 class="text-xl sub-title"><?= $sub_title ?></h2>
    <?php endif; ?>
    <?php if ($title): ?>
      <h3 class="bold title"><?= $title ?></h3>
    <?php endif; ?>
    <?php if ($description): ?>
      <div class="text-lg description"><?= $description ?></div>
    <?php endif; ?>
  </div>
    <?php if (!empty($image) && is_array($image)) { ?>
    <div class="right-image">
      <picture class="image image-wrapper cover-image ">
        <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
      </picture>
  </div>
    <?php } ?>
  </div>


  <div class="content-wrapper">

    <div class="left-content">
      <h4 class="text-xl text-uppercase white-color bold left-content-title">Our Services</h4>
      <div class="package-wrapper">
        <div class="package-box">
            <picture class="package-icon cover-image">
            </picture>
            <div class="title-and-excerpt">
              <div class="package-title text-xl white-color">SEO Optimization</div>
              <div class="package-description text-sm">Full SEO and content marketing for large businesses.</div>
            </div>
        </div>
        <div class="package-box">
          <picture class="package-icon cover-image">
          </picture>
          <div class="title-and-excerpt">
            <div class="package-title text-xl white-color">SEO Optimization</div>
            <div class="package-description text-sm">Full SEO and content marketing for large businesses.</div>
          </div>
        </div>
        <div class="package-box">
          <picture class="package-icon cover-image">
          </picture>
          <div class="title-and-excerpt">
            <div class="package-title text-xl white-color">SEO Optimization</div>
            <div class="package-description text-sm">Full SEO and content marketing for large businesses.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="right-content flex-col">
      <div class="info-box-card">
      <h5 class="text-xl text-uppercase white-color bold right-content-title">Help & Support</h5>
      <div class="info-box-wrapper">
        <div class="info-box">
        <picture class="info-box-image cover-image">
        </picture>
        <div class="package-title text-md semi-bold white-color">SEO Optimization

      </div>
      </div>
        <div class="info-box">
          <picture class="info-box-image cover-image">
          </picture>
          <div class="package-title text-md semi-bold white-color">SEO Optimization

          </div>
        </div>
        <div class="info-box">
          <picture class="info-box-image cover-image">
          </picture>
          <div class="package-title text-md semi-bold white-color">SEO Optimization

          </div>
        </div>
      </div>
      </div>
      <div class="info-box-card">
        <h5 class="text-xl text-uppercase white-color bold right-content-title">Help & Support</h5>
        <div class="info-box-wrapper">
          <div class="info-box">
            <picture class="info-box-image cover-image">
            </picture>
            <div class="package-title text-md semi-bold white-color">SEO Optimization

            </div>
          </div>
          <div class="info-box">
            <picture class="info-box-image cover-image">
            </picture>
            <div class="package-title text-md semi-bold white-color">SEO Optimization

            </div>
          </div>
          <div class="info-box">
            <picture class="info-box-image cover-image">
            </picture>
            <div class="package-title text-md semi-bold white-color">SEO Optimization

            </div>
          </div>
        </div>
      </div>
    </div>




</div>
  </div>
</section>


<!-- endregion threeSixty_theme's Block -->
