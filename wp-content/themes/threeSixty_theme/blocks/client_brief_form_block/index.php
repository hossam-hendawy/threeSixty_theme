<?php
// @author HOSSAM
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'client_brief_form_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/client_brief_form_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$form = get_field('form_short_code');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="get-in-touch">
    <div class="form-wrapper">
      <?= $form?>
    </div>
  </div>

</div>
</section>


<!-- endregion threeSixty_theme's Block -->
