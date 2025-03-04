<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'text_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/text_block/screenshot.png" >';

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
  <?php if (have_rows('content')) { ?>
    <div class="content-wrapper flex-col">
      <?php while (have_rows('content')) {
        the_row();
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        ?>
        <div class="content ">
          <?php if ($title) { ?>
            <h5 class="title d-sm-h5"><?= $title ?></h5>
          <?php } ?>
          <?php if ($description) { ?>
            <div class="description text-lg gray-500"><?= $description ?></div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
