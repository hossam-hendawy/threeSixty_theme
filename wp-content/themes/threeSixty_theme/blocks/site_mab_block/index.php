<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'site_mab_block';
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
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/site_mab_block/screenshot.png" >';

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
  <?php if (have_rows('content')) { ?>
    <div class="content-wrapper">
      <?php while (have_rows('content')) {
        the_row();
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        ?>
        <div class="content ">
          <?php if ($title) { ?>
            <h3 class="title d-lg-h3 bold"><?= $title ?></h3>
          <?php } ?>
          <?php if ($description) { ?>
            <div class="description text-md gray-500"><?= $description ?></div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
</div>
</section>


<!-- endregion threeSixty_theme's Block -->
