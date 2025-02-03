<?php
$post_id = @$args['post_id'] ?: get_the_ID();
$post_title = get_the_title($post_id);
$answer = get_field('answer', $post_id);
?>
<div class="accordion-panel" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
  <?php if ($post_title) { ?>
    <div id="panel2-title" class="title">
      <button class="accordion-trigger" aria-expanded="false" aria-controls="accordion1-content">
        <?= $post_title ?>
        <svg class="toggle-open minus-plus in-desk" width="20" height="20"
             viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M19.8425 1.23803L19.5242 0.919742C19.3135 0.709044 18.9728 0.709044 18.7621 0.919742L10.0025 9.68385L1.23841 0.919742C1.02771 0.709044 0.687008 0.709044 0.47631 0.919742L0.158023 1.23803C-0.0526743 1.44873 -0.0526743 1.78943 0.158023 2.00013L9.61699 11.4636C9.82769 11.6743 10.1684 11.6743 10.3791 11.4636L19.838 2.00013C20.0532 1.78943 20.0532 1.44873 19.8425 1.23803Z"
            fill="black"/>
        </svg>
      </button>
    </div>
  <?php } ?>
  <?php if ($answer) { ?>
    <div class="accordion-content" role="region" aria-labelledby="panel2-title" aria-hidden="true" id="panel2-content">
      <div class="body-6 answer regular" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
        <p class="spacer"></p>
        <?= $answer ?>
      </div>
    </div>
  <?php } ?>
</div>
