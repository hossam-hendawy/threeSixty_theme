<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link    https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package threeSixty_theme
 */
get_header();
$page_title = get_field('page_title', 'options');
$sub_title = get_field('sub_title', 'options');
$description = get_field('description', 'options');
?>
  <section id="page_not_found" class="page_not_found" data-section-class="page_not_found" aria-labelledby="page-not-found-heading"  >
    <div class="site-logo not-found-logo" role="img" aria-labelledby="not-found-logo-description">
      <svg  width="1344" height="389" viewBox="0 0 1344 389" fill="none">
        <title id="not-found-logo-description">404 Not Found Logo</title>
        <path d="M241.079 383V307.16H0.119141V227.96L241.079 1.39996H329.879V233.72H397.559V307.16H329.879V383H241.079ZM98.9991 233.72H242.039V99.7999L98.9991 233.72Z" fill="#EAAA08"/>
        <path d="M669.464 388.28C562.424 388.28 497.144 345.08 476.504 264.44C471.224 243.32 469.304 216.92 469.304 194.36C469.304 167.96 469.784 149.24 476.024 124.28C493.784 50.84 558.584 0.439941 669.464 0.439941C773.624 0.439941 840.824 49.88 860.984 118.52C867.704 141.56 869.624 166.04 869.624 194.84C869.624 218.84 866.264 249.56 860.504 269.72C837.944 346.52 767.384 388.28 669.464 388.28ZM669.464 317.24C725.144 317.24 759.704 293.24 770.264 253.4C774.104 239.96 776.024 212.6 776.024 193.88C776.024 173.72 773.624 146.84 768.344 131.48C756.344 96.44 725.144 72.92 669.944 72.92C613.304 72.92 578.744 97.3999 568.184 135.8C564.344 150.2 561.944 177.08 561.944 194.36C561.944 216.44 564.824 239 568.664 253.88C579.704 294.68 614.744 317.24 669.464 317.24Z" fill="#EAAA08"/>
        <path d="M1186.92 383V307.16H945.963V227.96L1186.92 1.39996H1275.72V233.72H1343.4V307.16H1275.72V383H1186.92ZM1044.84 233.72H1187.88V99.7999L1044.84 233.72Z" fill="#EAAA08"/>
      </svg>
    </div>
    <div class="container">
      <div class="content-wrapper flex-col gab-20">
        <h1 class="center-text">Page Not Found</h1>
        <p class="center-text">Oops! The page you were looking for doesn’t exit.</p>
        <a class="theme-cta-button" href="<?= site_url() ?>">home</a>
      </div>
    </div>
  </section>
<?php
get_footer();
