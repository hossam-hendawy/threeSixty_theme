<!--  please make the image field returned ID-->
<?php
$image = get_field('image');
?>
<div>
  <?php
  $picture_class = 'aspect-ratio';
  echo bis_get_attachment_picture(
    $image,
    [
      375 => [156, 191, 1],
      1024 => [165, 202, 1],
      1280 => [208, 255, 1],
      1440 => [234, 287, 1],
      1920 => [314, 385, 1],
      3840 => [314, 385, 1]
    ],
    [
      'retina' => true, 'picture_class' => $picture_class,
    ],
  );
  ?>
</div>
