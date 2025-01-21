<?php

// region Headline shortcode
add_shortcode('headline', 'headline_function');
function headline_function($atts = array())
{
  extract(shortcode_atts(array(
    'tag' => 'h1',
    'class' => 'headline-3',
    'text' => 'I am the big title',
    'color' => '#000'
  ), $atts));

  return "<$tag class='$class' style='color:$color'>$text</$tag>";
}

// endregion Headline shortcode
