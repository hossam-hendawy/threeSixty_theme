<?php
/*
 * Template Name: Entry Content
 * */

get_header();
?>
<?php while (have_posts()) : the_post(); ?>
  <div class="container">
    <div class="aaaa flex-col gab-20">
      <div class="text-xl regular gold text-center">Expert Perspectives on
        Digital
        Growth
      </div>
      <h2 class="d-lg-h3 text-center bold black"> Insights: Exploring the Future
        of
        Web Presence</h2>
      <div class="text-xl regular gray-500 text-center">At ThreeSixty, we are
        committed to helping businesses navigate the
        ever-changing world of web presence. Our Insights page brings you
        thought-provoking articles, expert analysis, and actionable strategies
        to
        help you understand and prepare for the future of the digital world.
        From
        the latest trends in SEO and social media management to advanced topics
        like
        AI-driven marketing and cybersecurity, we’ve got you covered.
      </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <a href="#" class="theme-cta-button ">Get in touch
      <svg aria-hidden="true" width="18" height="21" viewBox="0 0 18 21" fill="none">
        <path d="M11.878 20.23H0.38L6.156 10.22L11.878 20.23Z" fill="#9AA4B2"/>
        <path d="M17.621 10.231L11.881 0.23H0.38L6.155 10.22L11.878 20.23L17.621 10.231Z" fill="#F9F9FB"/>
      </svg>
    </a>
    <br>
    <br>
    <br>
    <br> <br>
    <br>
    <br>
    <br>

    <div class="cta-link text-sm gray-600 semi-bold">
      <svg width="20" height="21" viewBox="0 0 20 21" fill="none" aria-hidden="true" class="arrow">
        <path d="M15.8337 10.2297H4.16699M4.16699 10.2297L10.0003 16.0631M4.16699 10.2297L10.0003 4.3964" stroke="#4B5565" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Previous
    </div>


  </div>
  <?php the_content(); ?>
<?php endwhile; ?>
<?php get_footer();
