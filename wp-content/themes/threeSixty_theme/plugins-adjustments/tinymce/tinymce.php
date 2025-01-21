<?php
//region tinymce hook on init add gray background
function my_theme_add_editor_styles()
{
  add_editor_style('custom-tinymce-editor-style.css');
}

add_action('init', 'my_theme_add_editor_styles');
//endregion tinymce hook on init add gray background
