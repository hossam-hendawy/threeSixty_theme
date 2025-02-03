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
        <svg class="toggle-open minus-plus" width="24" height="26" viewBox="0 0 24 26" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 10V18M8 14H16M22 14C22 19.5228 17.5228 24 12 24C6.47715 24 2 19.5228 2 14C2 8.47715 6.47715 4 12 4C17.5228 4 22 8.47715 22 14Z" stroke="#98A2B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
