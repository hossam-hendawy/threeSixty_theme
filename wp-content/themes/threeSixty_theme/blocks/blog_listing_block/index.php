<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'blog_listing_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/blog_listing_block/screenshot.png" >';

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
  <div class="blog-cards">
    <div class="blog-card">
      <picture class="blog-image aspect-ratio">
        <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="trustpilot">
      </picture>
      <div class="blog-content flex-col">
        <h3 class="text-sm semi-bold job-name">Design</h3>
        <h4 class="d-sm-h5 semi-bold job-title">The Future of Web Presence: Trends for 2024 and Beyond</h4>
        <div class="text-md regular gray-600">How do you create compelling presentations that wow your colleagues and impress your managers?How do you create compelling presentations that wow your colleagues and impress your managers ...</div>
        <div class="about-author">
          <picture class="image-author">
            <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="trustpilot">
          </picture>
          <div class="author-info">
            <h5 class="text-sm semi-bold author-name">Dr. Muneer Hamed</h5>
          <h6 class="text-sm gray-600 author-jop">Senior Marketing Consultant</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
