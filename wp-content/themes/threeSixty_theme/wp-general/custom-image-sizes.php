<?php

//region custom image sizes

$image_dimensions_increased = 50;

function custom_image_sizes() {
  global $image_dimensions_increased;
  /* Please follow the following template to build all image sizes
  /*  add_image_size('img-48-48', 48 + $image_dimensions_increased, 48 + $image_dimensions_increased);
  image naming configuration
    ***** Round numbers to the nearest number up
    width = image width
    height =  image height
  */


  // region  sizes between 100
  \Theme\Helpers::add_image_size('img-48-48', 48, 48);
  \Theme\Helpers::add_image_size('img-110-75', 110, 75);
  \Theme\Helpers::add_image_size('img-213-44', 213, 44);
  \Theme\Helpers::add_image_size('img-136-20', 136, 20);
  \Theme\Helpers::add_image_size('img-588-600', 588, 600);


  // endregion  sizes between 100

  //  region sizes between  200

  //  endregion sizes between  200

  //  region sizes between  300
  \Theme\Helpers::add_image_size('img-384-280', 384, 280);
  \Theme\Helpers::add_image_size('img-383-280', 383, 280);
  \Theme\Helpers::add_image_size('img-384-384', 384, 384);
  \Theme\Helpers::add_image_size('img-316-476', 316, 476);

  \Theme\Helpers::add_image_size('img-343-250', 343, 250);
  //  endregion sizes between  300

  //  region sizes between  400

  //  region sizes between  500

  //  endregion sizes between  500
  \Theme\Helpers::add_image_size('img-588-605', 588, 605);

  //  region sizes between  600

  //  endregion sizes between  600

  //  region sizes between  1200
  \Theme\Helpers::add_image_size('img-1200-420', 1200, 420);
  \Theme\Helpers::add_image_size('img-1200-420', 1200, 415);
  //  endregion sizes between  1200
}

custom_image_sizes();

//endregion custom image sizes

