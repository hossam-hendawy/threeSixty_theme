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
        <span class="toggle-open minus-plus svg-border">
          <svg fill="#98A2B3" width="12px" height="12px" viewBox="0 0 45.402 45.402">

  <path d="M41.267,18.557H26.832V4.134C26.832,1.851,24.99,0,22.707,0c-2.283,0-4.124,1.851-4.124,4.135v14.432H4.141   c-2.283,0-4.139,1.851-4.138,4.135c-0.001,1.141,0.46,2.187,1.207,2.934c0.748,0.749,1.78,1.222,2.92,1.222h14.453V41.27   c0,1.142,0.453,2.176,1.201,2.922c0.748,0.748,1.777,1.211,2.919,1.211c2.282,0,4.129-1.851,4.129-4.133V26.857h14.435   c2.283,0,4.134-1.867,4.133-4.15C45.399,20.425,43.548,18.557,41.267,18.557z"/>
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
