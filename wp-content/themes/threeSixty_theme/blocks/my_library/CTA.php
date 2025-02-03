<!-- cta button code   -->
<?php
$cta_button = get_field('cta_button');
?>
<?php if (!empty($cta_button) && is_array($cta_button)) { ?>
  <a class="cta-button" href="<?= $cta_button['url'] ?>" target="<?= $cta_button['target'] ?>"><?= $cta_button['title'] ?></a>
<?php } ?>
