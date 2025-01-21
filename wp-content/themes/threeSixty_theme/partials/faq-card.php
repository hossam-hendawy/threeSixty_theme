<?php
$post_id = @$args['post_id'] ?: get_the_ID();
$post_title = get_the_title($post_id);
$answer = get_field('answer', $post_id);
?>
<div class="accordion-panel" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
  <?php if ($post_title) { ?>
    <div id="panel2-title" class="title">
      <button class="accordion-trigger" aria-expanded="false" aria-controls="accordion1-content">
         <span>
        <?= $post_title ?>
         </span>
        <svg class="toggle-open minus-plus" width="31" height="31" viewBox="0 0 31 31" fill="none" aria-hidden="true">
          <line class="vertical-line" x1="31" y1="16.5" x2="-8.74228e-08" y2="16.5" stroke="white" stroke-width="2"></line>
          <line class="horizontal-line" x1="16" y1="4.37103e-08" x2="16" y2="31" stroke="white" stroke-width="2"></line>
        </svg>
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
