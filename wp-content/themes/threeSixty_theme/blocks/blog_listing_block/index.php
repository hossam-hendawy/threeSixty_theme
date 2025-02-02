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
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="post-cards top-content-wrapper">
    <?php
    $args = array(
      'post_type' => 'post',
      'posts_per_page' => 2, // هنجيب أول بوستين بس
      'orderby' => 'date',
      'order' => 'DESC'
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) :
      while ($query->have_posts()) : $query->the_post();
        get_template_part("partials/vertical-post-card", "", ["post_id" => get_the_ID()]);
        ?>
      <?php endwhile;
      wp_reset_postdata();
    endif;
    ?>
  </div>
  <div class="bottom-content-wrapper">
    <div id="post-container">
      <div id="loading-spinner" class="loading-spinner" style="display: none;"></div>

    </div>
  </div>
  <div class="controllers">
    <div id="prev-page" class="cta-link text-sm gray-600 semi-bold">
      <svg width="20" height="21" viewBox="0 0 20 21" fill="none" aria-hidden="true" class="arrow">
        <path d="M15.8337 10.2297H4.16699M4.16699 10.2297L10.0003 16.0631M4.16699 10.2297L10.0003 4.3964" stroke="#4B5565" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>
      Previous
    </div>
    <div class="numbers"></div>
    <div id="next-page" class="cta-link text-sm gray-600 semi-bold">
      Next
      <svg width="20" height="21" viewBox="0 0 20 21" fill="none" class="arrow" aria-hidden="true">
        <path d="M4.16699 10.2297H15.8337M15.8337 10.2297L10.0003 4.3964M15.8337 10.2297L10.0003 16.0631" stroke="#4B5565" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>
    </div>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
