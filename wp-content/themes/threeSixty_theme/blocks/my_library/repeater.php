<?php if (have_rows('statistics')) { ?>
  <div class="statistics-wrapper">
    <?php while (have_rows('statistics')) {
      the_row();
      $number = get_sub_field('number');
      $text = get_sub_field('text');
      ?>
      <div class="statistic">
        <?php if ($number) { ?>
          <div class="number sans-h2 off-white-color"><?= $number ?></div>
        <?php } ?>
        <?php if ($text) { ?>
          <div class="text body gray-color capital-textw"><?= $text ?></div>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
<?php } ?>
