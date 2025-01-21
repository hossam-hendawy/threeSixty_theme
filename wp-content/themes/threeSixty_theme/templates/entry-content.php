<?php
/*
 * Template Name: Entry Content
 * */

get_header();
?>

<?php while (have_posts()) : the_post(); ?>

  <div class="text-xl regular gold text-center">Expert Perspectives on Digital
    Growth
  </div>
  <br>
  <br>
  <h2 class="d-lg-h3 text-center bold black"> Insights: Exploring the Future of
    Web Presence</h2>
  <br>
  <br>
  <div class="text-xl regular gray-500 text-center">At ThreeSixty, we are committed to helping businesses navigate the
    ever-changing world of web presence. Our Insights page brings you
    thought-provoking articles, expert analysis, and actionable strategies to
    help you understand and prepare for the future of the digital world. From
    the latest trends in SEO and social media management to advanced topics like
    AI-driven marketing and cybersecurity, we’ve got you covered.
  </div>
  <?php the_content(); ?>
<?php endwhile; ?>
<?php get_footer();
