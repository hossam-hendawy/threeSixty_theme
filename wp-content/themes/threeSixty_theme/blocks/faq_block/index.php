<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'faq_block';
$has_border = get_field('has_border');
$has_border = $has_border ? ' block-has-border' : ' ';
if (isset($block)) {
  $id = 'block_' . uniqid();
  if (!empty($block['anchor'])) {
    $id = $block['anchor'];
  }
  $className .= $has_border;
// Create class attribute allowing for custom "className" and "align" values.
  if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
  }
  if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
  }
  if (get_field('is_screenshot')) :
    /* Render screenshot for example */
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/faq_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
$faqs_posts = get_field('faq_card');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="accordion">
    <div class="content flex-col gab-20">
      <?php if ($sub_title): ?>
        <div class="text-xl sub-title uppercase-text"><?= $sub_title ?></div>
      <?php endif; ?>
      <?php if ($title): ?>
        <div class="d-lg-h3 bold main-title title"><?= $title ?></div>
      <?php endif; ?>
      <?php if ($description): ?>
        <div class="text-xl description center-text"><?= $description ?></div>
      <?php endif; ?>
    </div>
    <?php if (have_rows('faq_card')) { ?>
      <?php while (have_rows('faq_card')) {
        the_row();
        $question = get_sub_field('question');
        $answer = get_sub_field('answer');
        ?>
        <div class="accordion-panel" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <?php if ($question) { ?>
            <div id="panel2-title" class="title">
              <button class="accordion-trigger medium" aria-expanded="false">
                <span><?= $question ?></span>
                <span class="toggle-open minus-plus">
              <svg width="50" height="50" viewBox="0 0 50 50" fill="none" aria-hidden="true">
                <line class="vertical-line" x1="25" y1="5" x2="25" y2="45" stroke="#98A2B3" stroke-width="5" stroke-linecap="round"></line>
                <line class="horizontal-line" x1="5" y1="25" x2="45" y2="25" stroke="#98A2B3" stroke-width="5" stroke-linecap="round"></line>
              </svg>
            </span>
              </button>
            </div>
          <?php } ?>
          <?php if ($answer) { ?>
            <div class="accordion-content" role="region" aria-labelledby="panel2-title">
              <div class="answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                <p class="spacer"></p>
                <?= $answer ?>
              </div>
            </div>
          <?php } ?>
        </div>
      <?php } ?>
    <?php } ?>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
