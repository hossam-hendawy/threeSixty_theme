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


<script>
  // Define a global array to hold the results
  let imageSizeHistoryArray = [];

  function logImageSizeHistoryArray(className) {
    const viewportWidth = window.innerWidth; // Current viewport width

    // Query the first image with the given class name as an example
    const image = document.querySelector(className);
    if (image) {
      // Construct the entry for this viewport width with the image's rendered size
      const entry = [viewportWidth, [image.offsetWidth, image.offsetHeight, 1]];

      // Check if the viewportWidth already exists in the imageSizeHistoryArray
      const existingIndex = imageSizeHistoryArray.findIndex(el => el[0] === viewportWidth);

      // If it exists, update it. Otherwise, add the new entry.
      if (existingIndex !== -1) {
        imageSizeHistoryArray[existingIndex] = entry;
      } else {
        imageSizeHistoryArray.push(entry);
      }
    }

    // Sort the array by viewportWidth for better readability
    imageSizeHistoryArray.sort((a, b) => a[0] - b[0]);

    // Format the array to the desired string representation
    const formattedHistory = imageSizeHistoryArray.map(item => `${item[0]} => [${item[1].join(', ')}]`);

    // Log the formatted string representation
    console.log("[\n " + formattedHistory.join(",\n ") + "\n]");
  }

   logImageSizeHistoryArray('picture.image.aspect-ratio.aspect-ratio-5x4');


</script>
