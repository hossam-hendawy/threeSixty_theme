<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'who_we_are_block';
if (isset($block)) {
  $id = 'block_' . uniqid();
  if (!empty($block['anchor'])) {
    $id = $block['anchor'];
  }

// Create class attribute allowing for custom "className" and "align" values.
  if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
  }
  if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
  }
  if (get_field('is_screenshot')) :
    /* Render screenshot for example */
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/who_we_are_block/screenshot.png" >';

    return;
  endif;
}/****************************
 *     Custom ACF Meta      *
 ****************************/;
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
$cta_button = get_field('cta_button');
$image = get_field('image');
$current_lang = apply_filters('wpml_current_language', NULL);

?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="cards-wrapper">
    <div class="left-content flex-col gab-20 ">
      <?php if ($sub_title): ?>
        <h1 class="text-xl sub-title iv-st-from-bottom"><?= $sub_title ?></h1>
      <?php endif; ?>
      <?php if ($title): ?>
        <div class="title d-lg-h3 iv-st-from-bottom"><?= $title ?></div>
      <?php endif; ?>
      <?php if ($description): ?>
        <div class="text-lg description iv-st-from-bottom"><?= $description ?></div>
      <?php endif; ?>
      <?php if (!empty($cta_button) && is_array($cta_button)) { ?>
        <a class="theme-cta-button btn-white left-content-btn iv-st-from-bottom" href="<?= $cta_button['url'] ?>" target="<?= $cta_button['target'] ?>">
          <?= $cta_button['title'] ?>
          <svg width="25" height="29" viewBox="0 0 25 29" aria-hidden="true" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_1377_4705)">
              <path d="M16.6718 29L1.08116e-05 28.9549L8.37476 14.4851L16.6718 29Z" fill="#CA8504"/>
              <path d="M25 14.5012L16.6774 -3.63794e-07L1.90735e-06 -1.09278e-06L8.37476 14.4851L16.6717 29L25 14.5012Z" fill="#EAAA08"/>
            </g>
            <defs>
              <clipPath id="clip0_1377_4705">
                <rect width="29" height="25" fill="white" transform="translate(25) rotate(90)"/>
              </clipPath>
            </defs>
          </svg>
        </a>
      <?php } ?>
    </div>
    <div class="right-image">
      <?php if (!empty($image) && is_array($image)) { ?>
        <picture class="image image-wrapper cover-image">
          <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
        </picture>
      <?php } ?>
    </div>
    <?php if ($current_lang === 'ar') { ?>
      <svg class="logo" width="1440" height="738" viewBox="0 0 1440 738" fill="none" aria-hidden="true">
        <path d="M227 736.754L343.151 552.681H343.206L459.357 736.754H227Z" stroke="#D0D5DD" stroke-width="0.7"
              stroke-miterlimit="10"></path>
        <path d="M343.18 553.039L459.33 368.966H459.386L575.537 553.039H343.18Z" stroke="#D0D5DD" stroke-width="0.7"
              stroke-miterlimit="10"></path>
        <path d="M459.719 368.966L575.869 184.894H575.925L692.076 368.966H459.719Z" stroke="#D0D5DD" stroke-width="0.7"
              stroke-miterlimit="10"></path>
        <path d="M343.18 185.072L459.33 1H459.386L575.537 185.072H343.18Z" stroke="#D0D5DD" stroke-width="0.7"
              stroke-miterlimit="10"></path>
        <path d="M692.076 368.787L575.925 552.859H575.869L459.719 368.787H692.076Z" stroke="#D0D5DD" stroke-width="0.7"
              stroke-miterlimit="10"></path>
        <path d="M575.537 184.996L459.386 369.068H459.33L343.18 184.996H575.537Z" stroke="#D0D5DD" stroke-width="0.7"
              stroke-miterlimit="10"></path>
        <path d="M459.724 1L343.574 185.072H343.518L227.367 1H459.724Z" stroke="#D0D5DD" stroke-width="0.7"
              stroke-miterlimit="10"></path>
        <path d="M575.896 552.681L459.746 736.754H459.69L343.539 552.681H575.896Z" stroke="#D0D5DD" stroke-width="0.7"
              stroke-miterlimit="10"></path>
        <g opacity="0.9">
          <path d="M401.631 460.9H343.897L372.764 415.152L401.631 460.9Z" fill="#4B5565" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M373.034 415.076H430.768L401.901 460.823L373.034 415.076Z" fill="#4B5565" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M430.677 414.965H372.943L401.81 369.218L430.677 414.965Z" fill="#4B5565" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M459.818 368.942H402.085L430.951 323.194L459.818 368.942Z" fill="#697586" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M402.085 369.097H459.818L430.951 414.844L402.085 369.097Z" fill="#697586" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M372.943 323.143H430.677L401.81 368.891L372.943 323.143Z" fill="#697586" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M314.936 323.054H372.67L343.803 368.801L314.936 323.054Z" fill="#CDD5DF" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M372.67 322.963H314.936L343.803 277.216L372.67 322.963Z" fill="#CDD5DF" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M343.987 277.139H401.721L372.854 322.887L343.987 277.139Z" fill="#CDD5DF" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M430.677 322.963H372.943L401.81 277.216L430.677 322.963Z" fill="#697586" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M112.625 415.076H170.358L141.492 460.823L112.625 415.076Z" fill="#4B5565" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M199.409 460.9H141.675L170.542 415.152L199.409 460.9Z" fill="#4B5565" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M170.722 415.076H228.456L199.589 460.823L170.722 415.076Z" fill="#4B5565" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M228.456 322.963H170.722L199.589 277.216L228.456 322.963Z" fill="#CDD5DF" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M170.358 322.963H112.625L141.492 277.216L170.358 322.963Z" fill="#CDD5DF" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M141.675 277.139H199.409L170.542 322.887L141.675 277.139Z" fill="#CDD5DF" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M228.456 414.921H170.722L199.589 369.173L228.456 414.921Z" fill="#697586" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M199.409 368.942H141.675L170.542 323.194L199.409 368.942Z" fill="#9AA3B2" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M141.675 369.097H199.409L170.542 414.844L141.675 369.097Z" fill="#9AA3B2" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M170.722 323.118H228.456L199.589 368.865L170.722 323.118Z" fill="#9AA3B2" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M358.618 414.921H300.884L329.751 369.174L358.618 414.921Z" fill="#697586" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M329.566 460.9H271.833L300.7 415.152L329.566 460.9Z" fill="#4B5565" stroke="#EEF2F6" stroke-width="0.2"
                stroke-miterlimit="10"></path>
          <path d="M271.474 460.9H213.74L242.607 415.152L271.474 460.9Z" fill="#4B5565" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M242.791 415.076H300.524L271.658 460.823L242.791 415.076Z" fill="#4B5565" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M300.884 415.076H358.618L329.751 460.823L300.884 415.076Z" fill="#697586" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M271.833 369.097H329.566L300.7 414.845L271.833 369.097Z" fill="#697586" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M271.474 368.967H213.74L242.607 323.22L271.474 368.967Z" fill="#9AA3B2" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M213.74 369.122H271.474L242.607 414.87L213.74 369.122Z" fill="#9AA3B2" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M242.791 323.118H300.524L271.658 368.865L242.791 323.118Z" fill="#9AA3B2" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
          <path d="M300.524 322.944H242.791L271.658 277.196L300.524 322.944Z" fill="#9AA3B2" stroke="#EEF2F6"
                stroke-width="0.2" stroke-miterlimit="10"></path>
        </g>
      </svg>
    <?php } elseif ($current_lang === 'en') {
      ?>
      <svg class="logo" width="727" height="738" viewBox="0 0 727 738" fill="none" aria-hidden="true">
        <path d="M1 736.754L117.151 552.681H117.206L233.357 736.754H1Z" stroke="#D0D5DD" stroke-width="0.7" stroke-miterlimit="10"/>
        <path d="M117.18 553.039L233.33 368.966H233.386L349.537 553.039H117.18Z" stroke="#D0D5DD" stroke-width="0.7" stroke-miterlimit="10"/>
        <path d="M233.719 368.966L349.869 184.894H349.925L466.076 368.966H233.719Z" stroke="#D0D5DD" stroke-width="0.7" stroke-miterlimit="10"/>
        <path d="M117.18 185.072L233.33 1H233.386L349.537 185.072H117.18Z" stroke="#D0D5DD" stroke-width="0.7" stroke-miterlimit="10"/>
        <path d="M466.076 368.787L349.925 552.859H349.869L233.719 368.787H466.076Z" stroke="#D0D5DD" stroke-width="0.7" stroke-miterlimit="10"/>
        <path d="M349.537 184.996L233.386 369.068H233.33L117.18 184.996H349.537Z" stroke="#D0D5DD" stroke-width="0.7" stroke-miterlimit="10"/>
        <path d="M233.724 1L117.574 185.072H117.518L1.36725 1H233.724Z" stroke="#D0D5DD" stroke-width="0.7" stroke-miterlimit="10"/>
        <path d="M349.896 552.681L233.745 736.754H233.69L117.539 552.681H349.896Z" stroke="#D0D5DD" stroke-width="0.7" stroke-miterlimit="10"/>
        <g opacity="0.9">
          <path d="M667.631 460.9H609.897L638.764 415.152L667.631 460.9Z" fill="#4B5565" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M639.034 415.076H696.768L667.901 460.823L639.034 415.076Z" fill="#4B5565" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M696.677 414.965H638.943L667.81 369.218L696.677 414.965Z" fill="#4B5565" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M725.819 368.942H668.085L696.952 323.194L725.819 368.942Z" fill="#697586" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M668.085 369.097H725.819L696.952 414.844L668.085 369.097Z" fill="#697586" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M638.943 323.143H696.677L667.81 368.891L638.943 323.143Z" fill="#697586" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M580.937 323.054H638.67L609.804 368.801L580.937 323.054Z" fill="#CDD5DF" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M638.67 322.963H580.937L609.804 277.216L638.67 322.963Z" fill="#CDD5DF" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M609.987 277.139H667.721L638.854 322.887L609.987 277.139Z" fill="#CDD5DF" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M696.677 322.963H638.943L667.81 277.216L696.677 322.963Z" fill="#697586" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M378.625 415.076H436.359L407.492 460.823L378.625 415.076Z" fill="#4B5565" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M465.41 460.9H407.676L436.543 415.152L465.41 460.9Z" fill="#4B5565" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M436.723 415.076H494.457L465.59 460.823L436.723 415.076Z" fill="#4B5565" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M494.457 322.963H436.723L465.59 277.216L494.457 322.963Z" fill="#CDD5DF" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M436.359 322.963H378.625L407.492 277.216L436.359 322.963Z" fill="#CDD5DF" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M407.676 277.139H465.41L436.543 322.887L407.676 277.139Z" fill="#CDD5DF" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M494.457 414.921H436.723L465.59 369.173L494.457 414.921Z" fill="#697586" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M465.41 368.942H407.676L436.543 323.194L465.41 368.942Z" fill="#9AA3B2" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M407.676 369.097H465.41L436.543 414.844L407.676 369.097Z" fill="#9AA3B2" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M436.723 323.118H494.457L465.59 368.865L436.723 323.118Z" fill="#9AA3B2" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M624.618 414.921H566.885L595.751 369.174L624.618 414.921Z" fill="#697586" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M595.567 460.9H537.833L566.7 415.152L595.567 460.9Z" fill="#4B5565" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M537.474 460.9H479.74L508.607 415.152L537.474 460.9Z" fill="#4B5565" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M508.791 415.076H566.525L537.658 460.823L508.791 415.076Z" fill="#4B5565" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M566.885 415.076H624.618L595.751 460.823L566.885 415.076Z" fill="#697586" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M537.833 369.097H595.567L566.7 414.845L537.833 369.097Z" fill="#697586" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M537.474 368.967H479.74L508.607 323.22L537.474 368.967Z" fill="#9AA3B2" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M479.74 369.122H537.474L508.607 414.87L479.74 369.122Z" fill="#9AA3B2" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M508.791 323.118H566.525L537.658 368.865L508.791 323.118Z" fill="#9AA3B2" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
          <path d="M566.525 322.944H508.791L537.658 277.196L566.525 322.944Z" fill="#9AA3B2" stroke="#EEF2F6" stroke-width="0.2" stroke-miterlimit="10"/>
        </g>
      </svg>
    <?php } ?>
  </div>
</div>
</section>
<!-- endregion threeSixty_theme's Block -->
