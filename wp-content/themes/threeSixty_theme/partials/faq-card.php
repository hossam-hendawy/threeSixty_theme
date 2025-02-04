<?php
$post_id = @$args['post_id'] ?: get_the_ID();
$post_title = get_the_title($post_id);
$answer = get_field('answer', $post_id);
?>
<div class="accordion-panel" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
  <?php if ($post_title) { ?>
    <div id="panel2-title" class="title">
      <button class="accordion-trigger medium" aria-expanded="false" aria-controls="accordion1-content">
         <span>
        <?= $post_title ?>
         </span>
        <span class="toggle-open minus-plus">
             <svg width="50" height="50" viewBox="0 0 50 50" fill="none" aria-hidden="true">
          <line class="vertical-line" x1="25" y1="5" x2="25" y2="45" stroke="#98A2B3" stroke-width="5" stroke-linecap="round"></line>
          <line class="horizontal-line" x1="5" y1="25" x2="45" y2="25" stroke="#98A2B3" stroke-width="5" stroke-linecap="round"></line>
        </svg>
        </span>
      </button>
    </div>
  <?php } ?>
  <?php if ($answer) { ?>
    <div class="accordion-content" role="region" aria-labelledby="panel2-title" aria-hidden="true" id="panel2-content">
      <div class="answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
        <p class="spacer"></p>
        <?= $answer ?>
      </div>
    </div>
  <?php } ?>
</div>
