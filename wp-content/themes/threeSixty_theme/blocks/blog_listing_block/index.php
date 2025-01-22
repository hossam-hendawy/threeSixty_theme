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
  <div class="post-cards top-content-wrapper">
    <div class="post-card">
      <a href="#" class="post-image-card">
        <picture class="post-image aspect-ratio">
          <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="post-title">
        </picture>
      </a>

      <div class="post-content flex-col">
        <div class="text-sm semi-bold category">Design</div>
        <a href="#" class="d-sm-h5 semi-bold post-title">The Future of Web
          Presence:
          Trends for 2024 and Beyond</a>
        <div class="text-md regular gray-600">How do you create compelling
          presentations that wow your colleagues and impress your managers?How
          do you create compelling presentations that wow your colleagues and
          impress your managers ...
        </div>
        <div class="about-author">
          <picture class="image-author">
            <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="trustpilot">
          </picture>
          <div class="author-info">
            <h5 class="text-sm semi-bold author-name">Dr. Muneer Hamed</h5>
            <h6 class="text-sm gray-600 author-jop">Senior Marketing
              Consultant</h6>
          </div>
        </div>
      </div>
    </div>
    <div class="post-card">
      <a href="#" class="post-image-card">
        <picture class="post-image aspect-ratio">
          <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="post-title">
        </picture>
      </a>

      <div class="post-content flex-col">
        <div class="text-sm semi-bold category">Design</div>
        <a href="#" class="d-sm-h5 semi-bold post-title">The Future of Web
          Presence:
          Trends for 2024 and Beyond</a>
        <div class="text-md regular gray-600">How do you create compelling
          presentations that wow your colleagues and impress your managers?How
          do you create compelling presentations that wow your colleagues and
          impress your managers ...
        </div>
        <div class="about-author">
          <picture class="image-author">
            <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="trustpilot">
          </picture>
          <div class="author-info">
            <h5 class="text-sm semi-bold author-name">Dr. Muneer Hamed</h5>
            <h6 class="text-sm gray-600 author-jop">Senior Marketing
              Consultant</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="bottom-content-wrapper">
    <div class="post-card horizontal-card">
      <a href="#" class="post-image-card">
        <picture class="post-image aspect-ratio">
          <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="post-title">
        </picture>
      </a>
      <div class="post-content flex-col">
        <h3 class="text-sm semi-bold category">Design</h3>
        <a href="#" class="d-sm-h5 semi-bold post-title">The Future of Web
          Presence:
          Trends for 2024 and Beyond</a>
        <div class="text-md regular gray-600">How do you create compelling
          presentations that wow your colleagues and impress your managers?How
          do
          you create compelling presentations that wow your colleagues and
          impress
          your managers ...
        </div>
        <div class="about-author">
          <picture class="image-author">
            <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="Dr. Muneer Hamed">
          </picture>
          <div class="author-info">
            <h5 class="text-sm semi-bold author-name">Dr. Muneer Hamed</h5>
            <h6 class="text-sm gray-600 author-jop">Senior Marketing
              Consultant</h6>
          </div>
        </div>
      </div>
    </div>
    <div class="post-card horizontal-card">
      <a href="#" class="post-image-card">
        <picture class="post-image aspect-ratio">
          <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="post-title">
        </picture>
      </a>
      <div class="post-content flex-col">
        <h3 class="text-sm semi-bold category">Design</h3>
        <a href="#" class="d-sm-h5 semi-bold post-title">The Future of Web
          Presence:
          Trends for 2024 and Beyond</a>
        <div class="text-md regular gray-600">How do you create compelling
          presentations that wow your colleagues and impress your managers?How
          do
          you create compelling presentations that wow your colleagues and
          impress
          your managers ...
        </div>
        <div class="about-author">
          <picture class="image-author">
            <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="Dr. Muneer Hamed">
          </picture>
          <div class="author-info">
            <h5 class="text-sm semi-bold author-name">Dr. Muneer Hamed</h5>
            <h6 class="text-sm gray-600 author-jop">Senior Marketing
              Consultant</h6>
          </div>
        </div>
      </div>
    </div>
    <div class="post-card horizontal-card">
      <a href="#" class="post-image-card">
        <picture class="post-image aspect-ratio">
          <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="post-title">
        </picture>
      </a>
      <div class="post-content flex-col">
        <h3 class="text-sm semi-bold category">Design</h3>
        <a href="#" class="d-sm-h5 semi-bold post-title">The Future of Web
          Presence:
          Trends for 2024 and Beyond</a>
        <div class="text-md regular gray-600">How do you create compelling
          presentations that wow your colleagues and impress your managers?How
          do
          you create compelling presentations that wow your colleagues and
          impress
          your managers ...
        </div>
        <div class="about-author">
          <picture class="image-author">
            <img src=" <?= get_template_directory_uri() . '/images/backgrounds/author-image.png' ?>" alt="Dr. Muneer Hamed">
          </picture>
          <div class="author-info">
            <h5 class="text-sm semi-bold author-name">Dr. Muneer Hamed</h5>
            <h6 class="text-sm gray-600 author-jop">Senior Marketing
              Consultant</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="controllers">
    <div class="cta-link text-sm gray-600 semi-bold">
      <svg width="20" height="21" viewBox="0 0 20 21" fill="none" aria-hidden="true" class="arrow">
        <path d="M15.8337 10.2297H4.16699M4.16699 10.2297L10.0003 16.0631M4.16699 10.2297L10.0003 4.3964" stroke="#4B5565" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Previous
    </div>
    <div class="numbers">
      <div class="number active text-sm medium gray-600">1</div>
      <div class="number text-sm medium gray-600">2</div>
      <div class="number text-sm medium gray-600">3</div>
      <div class="number text-sm medium gray-600">...</div>
      <div class="number text-sm medium gray-600">8</div>
      <div class="number text-sm medium gray-600">9</div>
      <div class="number text-sm medium gray-600">10</div>
    </div>
    <div class="cta-link text-sm gray-600 semi-bold">
      Next
      <svg width="20" height="21" viewBox="0 0 20 21" fill="none" class="arrow" aria-hidden="true">
        <path d="M4.16699 10.2297H15.8337M15.8337 10.2297L10.0003 4.3964M15.8337 10.2297L10.0003 16.0631" stroke="#4B5565" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
  </div>

</div>
</section>
<!-- endregion threeSixty_theme's Block -->
