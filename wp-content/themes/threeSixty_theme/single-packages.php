<?php
get_header();
global $post;
$post_id = get_the_ID();

// hero block
$package_target = get_field('package_target', $post_id);
$post_title = get_the_title($post_id);
$package_description = get_field('package_description', $post_id);

// package block
$package_title = get_field('package_title', $post_id);
$package_excerpt = get_field('package_excerpt', $post_id);
$package_price = get_field('package_price', $post_id);
$package_icon = get_field('package_icon', $post_id);
$package_includes_icon = get_field('package_includes_icon', $post_id);
$get_started = get_field('get_started', $post_id);
$note = get_field('note', $post_id);
$note_icon = get_field('note_icon', $post_id);
?>
<?php if (have_posts()): the_post(); ?>
  <div class="single-package-wrapper">
    <!--     hero block -->
    <section id="block_67a82e9404ae9" class="threeSixty_theme-block hero_package js-loaded" data-section-class="hero_package">
      <div class="container">
        <div class="content-wrapper flex-col">
          <?php if (function_exists('threeSixty_theme_breadcrumbs')) {
            threeSixty_theme_breadcrumbs();
          } ?>
          <div class="sub-title-and-title">
            <?php if ($package_target) { ?>
              <div class="d-md-h4 fw-300 sub-title uppercase-text"><?= $package_target ?></div>
            <?php } ?>
            <?php if ($post_title) { ?>
              <h1 class="d-xl-h2 fw-700 white-color uppercase-text"><?= $post_title ?></h1>
            <?php } ?>
          </div>
          <?php if ($package_description) { ?>
            <div class="description text-xl white-color regular"><?= $package_description ?></div>
          <?php } ?>
        </div>
      </div>
    </section>
    <!--   package block  -->
    <section id="block_67a8210050f5e" class="threeSixty_theme-block package_block js-loaded" data-section-class="package_block">
      <div class="container">
        <div class="content-wrapper">
          <div class="left-content-wrapper flex-col">
            <?php if (have_rows('service_benefits')) { ?>
              <?php while (have_rows('service_benefits')) {
                the_row();
                $icon = get_sub_field('icon', $post_id);
                $title = get_sub_field('title', $post_id);
                $description = get_sub_field('description', $post_id);
                ?>
                <div class="service-benefit">
                  <?php if (!empty($icon) && is_array($icon)) { ?>
                    <picture class="icon-wrapper">
                      <img src="<?= $icon['url'] ?>" alt="<?= $icon['alt'] ?>">
                    </picture>
                  <?php } ?>
                  <div class="service-benefit-wrapper flex-col">
                    <?php if ($title): ?>
                      <div class="title text-xl bold"><?= $title ?></div>
                    <?php endif; ?>
                    <?php if ($description): ?>
                      <div class="text-md description regular gray-500"><?= $description ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php } ?>
            <?php } ?>
          </div>
          <div class="right-content-wrapper">
            <div class="package-box-wrapper">
              <div class="package-title-and-price">
                <div class="icon-and-package-title">
                  <?php if (!empty($package_icon) && is_array($package_icon)) { ?>
                    <picture class="package-icon-wrapper">
                      <img src="<?= $package_icon['url'] ?>" alt="<?= $package_icon['alt'] ?>">
                    </picture>
                  <?php } ?>
                  <div class="title-and-excerpt flex-col">
                    <?php if ($package_title): ?>
                      <div class="package-title d-xs-6 bold uppercase-text"><?= $package_title ?></div>
                    <?php endif; ?>
                    <?php if ($package_excerpt): ?>
                      <div class="text-md description regular gray-500"><?= $package_excerpt ?></div>
                    <?php endif; ?>
                  </div>
                </div>
                <?php if ($package_price): ?>
                  <div class="price-container">
                    <sub class="d-md-h4 semi-bold gray-600">$</sub>
                    <div class="price d-lg-h3 bold gray-600"><?= $package_price ?></div>
                  </div>
                <?php endif; ?>
              </div>
              <div class="package-includes">
                <div class="package-includes-title text-md semi-bold">This package includes:</div>
                <div class="package-includes-wrapper">
                  <?php if (have_rows('package_includes', $post_id)) { ?>
                    <?php while (have_rows('package_includes', $post_id)) {
                      the_row();
                      $text = get_sub_field('text');
                      ?>
                      <div class="text">
                        <?php if (!empty($package_includes_icon) && is_array($package_includes_icon)) { ?>
                          <picture class="icon">
                            <img src="<?= $package_includes_icon['url'] ?>" alt="<?= $package_includes_icon['alt'] ?>">
                          </picture>
                        <?php } ?>
                        <div class="the-text text-md medium"><?= $text ?></div>
                      </div>
                    <?php } ?>
                  <?php } ?>
                </div>
              </div>
              <div class="cta-button-wrapper">
                <?php if (!empty($get_started) && is_array($get_started)) { ?>
                  <a class="theme-cta-button uppercase-text" href="<?= $get_started['url'] ?>" target="<?= $get_started['target'] ?>">
                    <?= $get_started['title'] ?>
                    <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
                      <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"/>
                      <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"/>
                    </svg>
                  </a>
                <?php } ?>
              </div>
            </div>
            <div class="note-wrapper">
              <?php if (!empty($note_icon) && is_array($note_icon)) { ?>
                <picture class="note-icon-wrapper">
                  <img src="<?= $note_icon['url'] ?>" alt="<?= $note_icon['alt'] ?>">
                </picture>
              <?php } ?>
              <?php if ($note): ?>
                <div class="note-text text-sm regular"><?= $note ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php the_content(); ?>
  </div>
<?php endif; ?>
<?php
get_footer();
