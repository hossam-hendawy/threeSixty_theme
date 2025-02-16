<?php
get_header();
global $post;
$post_id = get_the_ID();
// hero block
$service_target = get_field('service_target', $post_id);
$post_title = get_the_title($post_id);
$service_description = get_field('service_description', $post_id);

?>
<?php if (have_posts()): the_post(); ?>
  <div class="single-services-wrapper">
    <!--     hero block -->
    <section id="block_67b2101a9ff74" class="threeSixty_theme-block service_hero js-loaded" data-section-class="service_hero">
      <div class="container">
        <div class="content-wrapper flex-col">
          <?php if (function_exists('threeSixty_theme_breadcrumbs')) {
            threeSixty_theme_breadcrumbs();
          } ?>
          <div class="sub-title-and-title">
            <?php if ($service_target) { ?>
              <div class="d-md-h4 title uppercase-text"><?= $service_target ?></div>
            <?php } ?>
            <?php if ($post_title) { ?>
              <h1 class="d-xl-h2 fw-700 white-color uppercase-text"><?= $post_title ?></h1>
            <?php } ?>
          </div>
          <?php if ($service_description) { ?>
            <div class="description text-xl white-color regular"><?= $service_description ?></div>
          <?php } ?>
        </div>
      </div>
    </section>
    <?php the_content(); ?>
  </div>
<?php endif; ?>
<?php
get_footer();
