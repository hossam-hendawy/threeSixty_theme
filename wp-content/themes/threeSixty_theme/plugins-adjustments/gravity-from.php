<?php

//region gravity form hooks
add_filter('gform_submit_button', 'form_submit_button', 10, 2);
function form_submit_button($button, $form)
{
  $form_button_text = 'INQUIRE';
  return "<button class='button gform_button theme-cta-button' id='gform_submit_button_{$form['id']}' aria-label='Submit Form'>$form_button_text</button>";
}

add_filter('gform_confirmation_anchor', '__return_false');
//endregion gravity form hooks


// custom input fields by gravity form
add_filter('gform_field_input', 'custom_fields_input', 10, 5);
function custom_fields_input($input, $field, $value, $lead_id, $form_id)
{
  if (!is_admin()) {
    // Check for text field with enableDateRange property
    if ($field->type == 'date') {
      $date_format = $field->dateFormat ?? 'mm/dd/yyyy'; // Default to 'mm/dd/yyyy' if not set
      ob_start();
      ?>
      <div
        class="swight-box-container swight-box-container-date-picker swight-box-container-single-date-picker swight-box-contains-required default-mb">
        <div class="swight-form-date-picker-box swight-box">
          <span class="small-title text-center final-date">MM-DD-YYYY</span>
          <button class="swight-form-add-date-picker swight-headline-2 text-center"
                  aria-label="Add Date Picker" title="Add Date Picker"
                  type="button">
            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M3.01074 9.34863H20.998V10.8486H3.01074V9.34863Z" fill="#3BB6B8"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M15.3589 12.9619H16.8675V14.4619H15.3589V12.9619Z" fill="#3BB6B8"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2544 12.9619H12.763V14.4619H11.2544V12.9619Z" fill="#3BB6B8"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M7.14111 12.9619H8.64968V14.4619H7.14111V12.9619Z" fill="#3BB6B8"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M15.3589 16.5566H16.8675V18.0566H15.3589V16.5566Z" fill="#3BB6B8"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2544 16.5566H12.763V18.0566H11.2544V16.5566Z" fill="#3BB6B8"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M7.14111 16.5566H8.64968V18.0566H7.14111V16.5566Z" fill="#3BB6B8"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M16.4907 2.5V7.04399H14.9907V2.5H16.4907Z" fill="#3BB6B8"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M9.01855 2.5V7.04399H7.51855V2.5H9.01855Z" fill="#3BB6B8"/>
              <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M2.92578 3.96094H21.0758V22.5003H2.92578V3.96094ZM4.42578 5.46094V21.0003H19.5758V5.46094H4.42578Z"
                    fill="#3BB6B8"/>
            </svg>

          </button>
        </div>
        <div class="swight-form-date-picker default-mb">
          <label class="swight-input-container-date">
            <input class="swight-input-date swight-input-single-date"
                   id="input_<?= $field->formId ?>_<?= $field->id ?>"
                   name="input_<?= $field->id ?>" type="text"
                   value="" <?= $field->isRequired ? 'required aria-required="true"' : '' ?>
                   data-date-format="<?= $date_format ?>"
                   style="display: none">
            <button id="submit-date-range"
                    class="swight-form-add-date-picker swight-headline-2 text-center confirm-btn"
                    aria-label="Submit Date Range" title="Submit Date Range"
                    type="button">
              <?= __('Confirm', 'toc_theme') ?>
            </button>

          </label>
        </div>
      </div>
      <?php
      $input = ob_get_clean();
    }
    return $input;
  }
}

add_action('gform_field_standard_settings', 'add_custom_text_settings_to_number_field', 10, 2);
function add_custom_text_settings_to_number_field($position, $form_id)
{
  if ($position == 25) {
    ?>
    <li class="pre_input_text_setting field_setting">
      <label for="pre_input_text_setting">
        Pre-Input Text
        <?php gform_tooltip("form_field_pre_input_text") ?>
      </label>
      <input type="text" id="pre_input_text_setting"
             onchange="SetFieldProperty('preInputText', this.value);"/>
    </li>
    <li class="post_input_text_setting field_setting">
      <label for="post_input_text_setting">
        Post-Input Text
        <?php gform_tooltip("form_field_post_input_text") ?>
      </label>
      <input type="text" id="post_input_text_setting"
             onchange="SetFieldProperty('postInputText', this.value);"/>
    </li>
    <?php
  }
}


add_action('gform_field_standard_settings', 'add_custom_text_settings_to_time_field', 10, 2);
function add_custom_text_settings_to_time_field($position, $form_id)
{
  if ($position == 25) {
    ?>
    <li class="pre_input_time_setting field_setting">
      <label for="pre_input_time_setting">
        Pre-Input Time
        <?php gform_tooltip("form_field_pre_input_time") ?>
      </label>
      <input type="text" id="pre_input_time_setting"
             onchange="SetFieldProperty('preInputTime', this.value);"/>
    </li>
    <li class="post_input_time_setting field_setting">
      <label for="post_input_time_setting">
        Post-Input Time
        <?php gform_tooltip("form_field_post_input_time") ?>
      </label>
      <input type="text" id="post_input_time_setting"
             onchange="SetFieldProperty('postInputTime', this.value);"/>
    </li>
    <?php
  }
}

// Custom settings for the text field type
add_action('gform_field_standard_settings', 'add_date_range_setting_to_text_field', 10, 2);
function add_date_range_setting_to_text_field($position, $form_id)
{
  if ($position == 25) {
    ?>
    <li class="enable_date_range_setting field_setting">
      <input type="checkbox" id="enable_date_range"
             onclick="SetFieldProperty('enableDateRange', this.checked);"/>
      <label for="enable_date_range" style="display:inline;">
        <?php _e("Enable Date Range", "your_text_domain"); ?>
        <?php gform_tooltip("form_field_enable_date_range") ?>
      </label>
    </li>
    <?php
  }
}

add_action('gform_field_standard_settings', 'add_grid_layout_setting', 10, 2);
function add_grid_layout_setting($position, $form_id)
{
  // Adjust the position to place the setting under the "Rules" section
  if ($position == 25) {
    ?>
    <li class="grid_layout_setting field_setting">
      <label for="grid_layout_size">
        <?php _e("Grid Layout", "your_text_domain"); ?>
        <?php gform_tooltip("form_field_grid_layout") ?>
      </label>
      <select id="grid_layout_size"
              onchange="SetFieldProperty('gridLayoutSize', this.value);">
        <option
          value=""><?php _e("Select grid size", "your_text_domain"); ?></option>
        <?php for ($i = 2; $i <= 6; $i++): ?>
          <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
      </select>
    </li>
    <?php
  }
}



// Add tooltips for the custom settings
add_filter('gform_tooltips', 'add_custom_text_tooltips');
function add_custom_text_tooltips($tooltips)
{
  $tooltips['form_field_pre_input_text'] = "<h6>Pre-Input Text</h6>Enter the text that will appear before the input.";
  $tooltips['form_field_post_input_text'] = "<h6>Post-Input Text</h6>Enter the text that will appear after the input.";
  $tooltips['form_field_pre_input_time'] = "<h6>Pre-Input Text</h6>Enter the text that will appear before the input.";
  $tooltips['form_field_post_input_time'] = "<h6>Post-Input Text</h6>Enter the text that will appear after the input.";
  $tooltips['form_field_enable_date_range'] = "<h6>Enable Date Range</h6>Check this box to enable the custom date range for this text field.";
  $tooltips['form_field_grid_layout'] = "<h6>Grid Layout</h6>Select the grid layout size for this field or leave it as default.";
  return $tooltips;
}

add_filter('gform_field_css_class', 'custom_grid_layout_class', 10, 3);
function custom_grid_layout_class($classes, $field, $form)
{
  // Check if the field type is radio or checkbox and the gridLayoutSize property is set
  if (($field->type == 'radio' || $field->type == 'checkbox') && isset($field->gridLayoutSize) && !empty($field->gridLayoutSize)) {
    // Add the custom grid-layout class
    $classes .= ' grid-layout list-grid-' . $field->gridLayoutSize;
  }
  return $classes;
}


