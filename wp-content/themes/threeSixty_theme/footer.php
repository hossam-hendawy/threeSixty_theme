<?php wp_footer(); ?>

<?php
$hide_get_in_touch = get_field('hide_get_in_touch', get_the_ID());
$get_in_touch_title = get_field('get_in_touch_title','options');
$image = get_field('get_in_touch_image', 'options');
$get_in_touch_description = get_field('get_in_touch_description' , 'options');
$get_in_touch_cta_button = get_field('get_in_touch_cta_button' , 'options');
?>
<?php if (!$hide_get_in_touch) {?>
<section id="block_6797a0278885a" class="threeSixty_theme-block get_in_touch_block" data-section-class="get_in_touch_block">
  <div class="container">
    <div class="join-us-card">
      <!-- image -->
      <?php if (!empty($image) && is_array($image)) { ?>
        <picture class="question-svg">
          <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
        </picture>
      <?php } ?>
      <?php if ($get_in_touch_title) { ?>
        <h3 class="text-xl semi-bold text-center membership-title"><?= $get_in_touch_title ?></h3>
      <?php } ?>
      <?php if ($get_in_touch_description) { ?>
        <div class="membership-info text-lg text-center"><?= $get_in_touch_description ?></div>
      <?php } ?>
      <?php if (!empty($get_in_touch_cta_button) && is_array($get_in_touch_cta_button)) { ?>
        <a class="theme-cta-button" href="<?= $get_in_touch_cta_button['url'] ?>" target="<?= $get_in_touch_cta_button['target'] ?>">
          <?= $get_in_touch_cta_button['title'] ?>
          <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
            <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"/>
            <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"/>
          </svg>
        </a>
      <?php } ?>
    </div>
  </div>
</section>
<?php } ?>
<!--Footer ACF-->
<?php
$footer_logo = get_field('footer_logo', 'options');
$icon = get_field('icon', 'options');
$main_title = get_field('main_title', 'options');
$title = get_field('title', 'options');
$form = get_field('form', 'options');
$first_column = get_field('first_column', 'options');
$second_column = get_field('second_column', 'options');
$third_column = get_field('third_column', 'options');
$fourth_column = get_field('fourth_column', 'options');
$contact_email = get_field('contact_email', 'options');
$contact_number = get_field('contact_number', 'options');
$code_before_end_of_body_tag = get_field('code_before_end_of_body_tag', 'options');
$footer_logo = get_field('footer_logo', 'options');
$footer_text = get_field('footer_text', 'options');
?>
<!--region footer-->
<footer>
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
      <div class="form-btn">
        <?php echo do_shortcode('[gravityform id="' . $form . '" ajax="true" title="false" description="false"]'); ?>
        <a class="theme-cta-button btn" href="">SUBSCRIBE</a>
      </div>
    </div>
    <div class="content-wrapper">
      <div class="left-content">
        <?php if ($footer_logo) : ?>
          <a href="<?= site_url() . '/' ?>" class="image-wrapper footer-logo">
            <?= \Theme\Helpers::get_image($footer_logo, 'large'); ?>
          </a>
        <?php endif; ?>
        </a>
        <div class="company-info flex-col">
          <?php if ($contact_number) : ?>
            <a href="tel:<?= $contact_number ?>" class="en-h6 white-color contact-number hover-effect">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.58685 5.90211C6.05085 6.86853 6.68337 7.77429 7.48443 8.57534C8.28548 9.37639 9.19124 10.0089 10.1577 10.4729C10.2408 10.5128 10.2823 10.5328 10.3349 10.5481C10.5218 10.6026 10.7513 10.5635 10.9096 10.4501C10.9542 10.4182 10.9923 10.3801 11.0685 10.3039C11.3016 10.0708 11.4181 9.95431 11.5353 9.87812C11.9772 9.59079 12.5469 9.59079 12.9889 9.87812C13.106 9.95431 13.2226 10.0708 13.4556 10.3039L13.5856 10.4338C13.9398 10.7881 14.117 10.9653 14.2132 11.1555C14.4046 11.5339 14.4046 11.9807 14.2132 12.3591C14.117 12.5494 13.9399 12.7265 13.5856 13.0808L13.4805 13.1859C13.1274 13.539 12.9508 13.7155 12.7108 13.8504C12.4445 14 12.0308 14.1075 11.7253 14.1066C11.45 14.1058 11.2619 14.0524 10.8856 13.9456C8.86333 13.3716 6.95509 12.2886 5.36311 10.6967C3.77112 9.10467 2.68814 7.19643 2.11416 5.17417C2.00735 4.79787 1.95395 4.60972 1.95313 4.33442C1.95222 4.02894 2.0598 3.61528 2.20941 3.34894C2.34424 3.10892 2.52078 2.93238 2.87386 2.5793L2.97895 2.47421C3.33325 2.11992 3.5104 1.94277 3.70065 1.84654C4.07903 1.65516 4.52587 1.65516 4.90424 1.84654C5.0945 1.94277 5.27164 2.11991 5.62594 2.47421L5.75585 2.60412C5.98892 2.83719 6.10546 2.95373 6.18165 3.07091C6.46898 3.51284 6.46898 4.08256 6.18165 4.52449C6.10546 4.64167 5.98892 4.75821 5.75585 4.99128C5.67964 5.06749 5.64154 5.10559 5.60965 5.15013C5.4963 5.30842 5.45717 5.53793 5.51165 5.72483C5.52698 5.77742 5.54694 5.81899 5.58685 5.90211Z" stroke="#EAAA08" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <?= $contact_number ?></a>
          <?php endif; ?>
          <?php if ($contact_email) : ?>
            <a href="mailto:<?= $contact_email ?>" class="body white-color hover-effect"><?= $contact_email ?>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.33301 4.66663L6.77629 8.47692C7.21707 8.78547 7.43746 8.93974 7.67718 8.9995C7.88894 9.05228 8.11041 9.05228 8.32217 8.9995C8.56189 8.93974 8.78228 8.78547 9.22306 8.47692L14.6663 4.66663M4.53301 13.3333H11.4663C12.5864 13.3333 13.1465 13.3333 13.5743 13.1153C13.9506 12.9236 14.2566 12.6176 14.4484 12.2413C14.6663 11.8134 14.6663 11.2534 14.6663 10.1333V5.86663C14.6663 4.74652 14.6663 4.18647 14.4484 3.75864C14.2566 3.38232 13.9506 3.07636 13.5743 2.88461C13.1465 2.66663 12.5864 2.66663 11.4663 2.66663H4.53301C3.4129 2.66663 2.85285 2.66663 2.42503 2.88461C2.0487 3.07636 1.74274 3.38232 1.55099 3.75864C1.33301 4.18647 1.33301 4.74652 1.33301 5.86663V10.1333C1.33301 11.2534 1.33301 11.8134 1.55099 12.2413C1.74274 12.6176 2.0487 12.9236 2.42503 13.1153C2.85285 13.3333 3.4129 13.3333 4.53301 13.3333Z" stroke="#EAAA08" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="right-content">
        <?php
        if (have_rows('first_column', 'options')) :
          while (have_rows('first_column', 'options')) :
            the_row();
            ?>
            <?php if (have_rows('footer_link')) : ?>
            <div class="links-wrapper flex-col gab-20">
              <?php while (have_rows('footer_link')) : the_row();
                $link = get_sub_field('link');
                ?>
                <?php if ($link) { ?>
                  <a class="text-sm medium link" href="<?= $link['url'] ?>" target="<?= $link['target'] ?>">
                    <svg class="link-svg" width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M0 10.5L4.5 6L0 1.5" stroke="#667085" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?= $link['title'] ?></a>
                <?php } ?>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>
          <?php endwhile;
        endif; ?>
        <?php
        if (have_rows('second_column', 'options')) :
          while (have_rows('second_column', 'options')) :
            the_row();
            ?>
            <?php if (have_rows('footer_link')) : ?>
            <div class="links-wrapper flex-col gab-20">
              <?php while (have_rows('footer_link')) : the_row();
                $link = get_sub_field('link');
                ?>
                <?php if ($link) { ?>
                  <a class="text-sm medium link" href="<?= $link['url'] ?>" target="<?= $link['target'] ?>">
                    <svg class="link-svg" width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M0 10.5L4.5 6L0 1.5" stroke="#667085" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?= $link['title'] ?></a>

                <?php } ?>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>
          <?php endwhile;
        endif; ?>
        <?php
        if (have_rows('third_column', 'options')) :
          while (have_rows('third_column', 'options')) :
            the_row();
            ?>
            <?php if (have_rows('footer_link')) : ?>
            <div class="links-wrapper flex-col gab-20">
              <?php while (have_rows('footer_link')) : the_row();
                $link = get_sub_field('link');
                ?>
                <?php if ($link) { ?>
                  <a class="text-sm medium link" href="<?= $link['url'] ?>" target="<?= $link['target'] ?>">
                    <svg class="link-svg" width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M0 10.5L4.5 6L0 1.5" stroke="#667085" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?= $link['title'] ?></a>

                <?php } ?>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>
          <?php endwhile;
        endif; ?>

        <?php
        if (have_rows('fourth_column', 'options')) :
          while (have_rows('fourth_column', 'options')) :
            the_row();
            ?>
            <?php if (have_rows('footer_link')) : ?>
            <div class="links-wrapper flex-col gab-20">
              <?php while (have_rows('footer_link')) : the_row();
                $link = get_sub_field('link');
                ?>
                <?php if ($link) { ?>
                  <a class="text-sm medium link" href="<?= $link['url'] ?>" target="<?= $link['target'] ?>">
                    <svg class="link-svg" width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M0 10.5L4.5 6L0 1.5" stroke="#667085" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?= $link['title'] ?></a>
                <?php } ?>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>
          <?php endwhile;
        endif; ?>
      </div>
    </div>
    <div class="social-links-policy">
      <?php if ($footer_text): ?>
        <div class="captions text-md regular white-color"><?= $footer_text ?></div>
      <?php endif; ?>
      <?php if (have_rows('social_links', 'options')) { ?>
        <div class="social-links-wrapper">
          <?php while (have_rows('social_links', 'options')) {
            the_row();
            $url = get_sub_field('url');
            $icon = get_sub_field('icon');
            ?>
            <a href="<?= $url ?>" target="_blank" class="social-link">
              <?php if (!empty($icon) && is_array($icon)) { ?>
                <picture class="icon-wrapper cover-image">
                  <img src="<?= $icon['url'] ?>" alt="<?= $icon['alt'] ?>">
                </picture>
              <?php } ?>
            </a>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>
</footer>
</main>
<?= $code_before_end_of_body_tag ?>
</body>
</html>
