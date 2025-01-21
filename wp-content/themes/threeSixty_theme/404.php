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
    <a target="_self" href="<?= site_url() ?>" class="site-logo" role="img" aria-labelledby="Site logo" aria-label="Go to homepage">
      <svg width="1379" height="374" viewBox="0 0 1379 374" fill="none" aria-hidden="true">
        <title id="site-logo">Site logo</title>
        <path fill-rule="evenodd" clip-rule="evenodd"
              d="M1093.25 4.7868C1000.71 18.2312 949.453 66.7558 962.363 128.701C973.694 183.071 1018.38 203.337 1164.35 220.286C1270.27 232.585 1289.86 241.525 1281.72 273.838C1269.16 323.656 1065.14 312.313 1051.83 261.056L1050.37 255.439C1049.92 253.718 1048.38 252.507 1046.61 252.479L960.761 251.127C958.394 251.09 956.528 253.134 956.772 255.495L957.85 265.877C965.466 339.392 1047.34 377.55 1189.29 373.74C1321.75 370.186 1383.2 327.565 1374.09 245.574C1367.33 184.819 1330.02 166.396 1176.46 147.977C1078.32 136.206 1057.6 130.079 1052.95 111.465C1045.85 83.0639 1073.26 71.8104 1150.02 71.6171C1222.59 71.435 1261.91 84.3483 1268.98 110.693L1270.28 115.575C1270.74 117.281 1272.27 118.477 1274.03 118.505L1359.79 119.856C1362.18 119.894 1364.05 117.81 1363.77 115.43L1362.64 105.95C1353.64 30.1793 1232.24 -15.4066 1093.25 4.7868ZM0.939896 75.6198C0.939896 77.806 2.70666 79.5782 4.88607 79.5782H155.81C157.989 79.5782 159.756 81.3504 159.756 83.5366V365.209C159.756 367.394 161.523 369.166 163.702 369.166H247.331C249.51 369.166 251.277 367.394 251.277 365.209V83.5366C251.277 81.3504 253.044 79.5782 255.223 79.5782H406.147C408.326 79.5782 410.092 77.806 410.092 75.6198V11.1396C410.092 8.95347 408.326 7.18126 406.147 7.18126H4.88607C2.70666 7.18126 0.939896 8.95347 0.939896 11.1396V75.6198ZM510.56 365.209C510.56 367.394 512.326 369.166 514.505 369.166H867.315C869.494 369.166 871.26 367.394 871.26 365.209V300.728C871.26 298.543 869.494 296.769 867.315 296.769H600.644C598.462 296.769 596.696 294.997 596.696 292.812V228.361C596.696 226.182 598.451 224.414 600.622 224.402L855.303 223.052C857.443 223.041 859.184 221.322 859.228 219.177L860.565 156.047C860.612 153.837 858.843 152.016 856.639 152.005L601.886 150.655C599.746 150.644 598.007 148.925 597.96 146.781L596.627 83.6206C596.58 81.4021 598.36 79.5782 600.572 79.5782H861.931C864.11 79.5782 865.876 77.806 865.876 75.6198V11.1396C865.876 8.95347 864.11 7.18126 861.931 7.18126H514.505C512.326 7.18126 510.56 8.95347 510.56 11.1396V365.209Z"
              fill="white"/>
      </svg>
    </a>
    <div class="site-logo not-found-logo" role="img" aria-labelledby="not-found-logo-description">
      <svg xmlns="http://www.w3.org/2000/svg" width="1344" height="389" viewBox="0 0 1344 389" fill="none">
        <title id="not-found-logo-description">404 Not Found Logo</title>
        <path d="M241.079 383V307.16H0.119141V227.96L241.079 1.39996H329.879V233.72H397.559V307.16H329.879V383H241.079ZM98.9991 233.72H242.039V99.7999L98.9991 233.72Z" fill="#F84E35"/>
        <path d="M669.464 388.28C562.424 388.28 497.144 345.08 476.504 264.44C471.224 243.32 469.304 216.92 469.304 194.36C469.304 167.96 469.784 149.24 476.024 124.28C493.784 50.84 558.584 0.439941 669.464 0.439941C773.624 0.439941 840.824 49.88 860.984 118.52C867.704 141.56 869.624 166.04 869.624 194.84C869.624 218.84 866.264 249.56 860.504 269.72C837.944 346.52 767.384 388.28 669.464 388.28ZM669.464 317.24C725.144 317.24 759.704 293.24 770.264 253.4C774.104 239.96 776.024 212.6 776.024 193.88C776.024 173.72 773.624 146.84 768.344 131.48C756.344 96.44 725.144 72.92 669.944 72.92C613.304 72.92 578.744 97.3999 568.184 135.8C564.344 150.2 561.944 177.08 561.944 194.36C561.944 216.44 564.824 239 568.664 253.88C579.704 294.68 614.744 317.24 669.464 317.24Z" fill="#F84E35"/>
        <path d="M1186.92 383V307.16H945.963V227.96L1186.92 1.39996H1275.72V233.72H1343.4V307.16H1275.72V383H1186.92ZM1044.84 233.72H1187.88V99.7999L1044.84 233.72Z" fill="#F84E35"/>
      </svg>
    </div>
    <div class="container">
      <div class="content-wrapper">
        <?php if ($page_title): ?>
          <h1 id="page-not-found-heading" class="main-title sans-h2 off-white-color text-center iv-st-from-bottom"><?= $page_title ?></h1>
        <?php endif; ?>

        <?php if ($sub_title): ?>
          <h2 class="subheading off-white-color text-center iv-st-from-bottom" aria-label="Subtitle"><?= $sub_title ?></h2>
        <?php endif; ?>

        <?php if ($description): ?>
          <div class="description body gray-color text-center iv-st-from-bottom" aria-label="Page Description"><?= $description ?></div>
        <?php endif; ?>

        <div class="links iv-st-from-bottom">
          <div class="subheading off-white-color text-center" aria-label="Useful Links">
            Links
          </div>
          <a class="body gray-color text-center" href="<?= site_url() ?>" target="_self" aria-label="Return to Homepage">Homepage</a>
          <?php if (have_rows('header_links', 'options')) { ?>
            <?php
            while (have_rows('header_links', 'options')) {
              the_row();
              $page_link = get_sub_field('link');
              ?>
              <?php if ($page_link) { ?>
                <a class="body gray-color text-center" href="<?= $page_link['url'] ?>" target="<?= $page_link['target'] ?>" aria-label="Link to <?= $page_link['title'] ?>">
                  <?= $page_link['title'] ?>
                </a>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        </div>
      </div>
    </div>
  </section>
<?php
get_footer();
