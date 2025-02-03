<!-- please make the image field returned ARRAY-->
<!-- we use this with the small images and icons-->

<?php
$image = get_field('image');
?>
<?php if (!empty($image) && is_array($image)) { ?>
  <picture class="image-wrapper">
    <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
  </picture>
<?php } ?>
