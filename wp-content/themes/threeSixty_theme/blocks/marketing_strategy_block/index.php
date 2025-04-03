<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'marketing_strategy_block';
if (isset($block)) {
  $id = 'block_' . uniqid();
  if (!empty($block['anchor'])) {
    $id = $block['anchor'];
  }
  $className .= '  block-has-border ';

// Create class attribute allowing for custom "className" and "align" values.
  if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
  }
  if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
  }
  if (get_field('is_screenshot')) :
    /* Render screenshot for example */
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/marketing_strategy_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$title = get_field('title');
$description = get_field('description');
$cta_button = get_field('cta_button');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="marketing-cards flex-col gab-20 iv-st-from-bottom">
    <?php if ($title) { ?>
      <h4 class="bold text-center marketing-title"><?= $title ?></h4>
    <?php } ?>
    <?php if ($description) { ?>
      <div class="marketing-info text-xl text-center"><?= $description ?></div>
    <?php } ?>
  <?php if (!empty($cta_button) && is_array($cta_button)) { ?>
    <a class="theme-cta-button marketing-btn" href="<?= $cta_button['url'] ?>" target="<?= $cta_button['target'] ?>">
      <?= $cta_button['title'] ?>
      <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
        <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"/>
        <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"/>
      </svg>
    </a>
  <?php } ?>
  </div>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
