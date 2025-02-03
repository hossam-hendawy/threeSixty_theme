<?php
// take the code form here
/**
 * Registers strings for translation with Polylang.
 *
 * This function checks if the Polylang plugin's `pll_register_string` function exists,
 * and if so, it registers a set of strings for translation. These strings are used
 * throughout the theme and are registered in the 'twentytwentyone' group.
 *
 * @return void
 * @since Twenty Twenty-One 1.0
 */
function twentytwentyone_register_polylang_strings()
{
  if (function_exists('pll_register_string')) {
    pll_register_string('Bread Type', 'Bread Type', 'twentytwentyone');
  }
}

add_action('init', 'twentytwentyone_register_polylang_strings');


/**
 * Sets the global variable for the current language using Polylang.
 *
 * This function checks if Polylang is active and sets a global variable
 * `$current_language` with the current language code. This variable can be
 * used globally across the theme for consistent language checks.
 *
 * @return void
 * @since Twenty Twenty-One 1.0
 */
function twentytwentyone_lobal_current_language() {
  global $current_language;
  if ( function_exists( 'pll_current_language' ) ) {
    $current_language = pll_current_language();
  } else {
    $current_language = 'ar';
  }
}

add_action( 'init', 'twentytwentyone_lobal_current_language' );

function auto_translate_to_arabic()
{
  if (function_exists('pll_register_string') && function_exists('pll_translate_string')) {

    // Array of translations
    $translations = array(
      'Bread Type' => 'نوع الخبز',
    );

    // Loop through the translations and update them
    foreach ($translations as $english => $arabic) {
      // Get the existing string's translation in Arabic
      $current_translation = pll_translate_string($english, 'ar');

      // If the translation is not already set, update it
      if ($current_translation !== $arabic) {
        $lang = PLL()->model->get_language('ar'); // Get Arabic language object
        $mo = new PLL_MO();
        $mo->import_from_db($lang);
        $mo->add_entry($mo->make_entry($english, $arabic));
        $mo->export_to_db($lang);
      }
    }
  }
}

// Hook into an appropriate action, like 'init' or 'admin_init'
//add_action('admin_init', 'auto_translate_to_arabic');
// to here


