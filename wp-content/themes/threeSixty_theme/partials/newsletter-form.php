<?php
$newsletter_subscription = get_field('newsletter_subscription', 'options');
$footer_logo = get_field('footer_logo', 'options');
$icon = get_field('icon', 'options');
$main_title = get_field('main_title', 'options');
$title = get_field('title', 'options');
$form = get_field('form', 'options');
?>
<section id="block_682a0b488e1d8" class="threeSixty_theme-block newsletter_block js-loaded" data-section-class="newsletter_block">
  <div class="container">
    <div class="contact-us-wrapper">
      <div class="content-icon">
        <?php if (!empty($icon) && is_array($icon)) { ?>
          <picture class="image-wrapper icon cover-image ">
            <img src="<?= $icon['url'] ?>" alt="<?= $icon['alt'] ?>">
          </picture>
        <?php } ?>
        <div class="content">
          <?php if ($main_title) { ?>
            <h3 class="text-lg semi-bold footer-title white-color"> <?= $main_title ?> </h3>
          <?php } ?>
          <?php if ($title) { ?>
            <h5 class="text-md regular footer-description"><?= $title ?></h5>
          <?php } ?>
        </div>
      </div>
      <?php if ($newsletter_subscription): ?>
        <div class="form-btn">
          <?php echo do_shortcode($newsletter_subscription); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

