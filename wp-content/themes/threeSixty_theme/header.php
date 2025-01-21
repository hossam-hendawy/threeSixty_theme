<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="description" content="<?php if (is_single()) {
    single_post_title('', true);
  } else {
    bloginfo('name');
    echo " - ";
    bloginfo('description');
  } ?>"/>
  <meta
    content="width=device-width, initial-scale=1.0, maximum-scale=5, minimum-scale=1.0"
    name="viewport">
  <meta content="ie=edge" http-equiv="X-UA-Compatible">

  <!-- fix container-->
  <style>

    html {
      --design-width: 1920;
      --tablet-width: 992;
      --mobile-width: 600;
      --sMobile-width: 375;
      font-size: calc((100vw / var(--sMobile-width)) * 10);
      width: 100vw;
      overflow-x: hidden;
      scrollbar-color: rgb(90, 90, 90) rgba(0, 0, 0, 0.2);
      scrollbar-width: thin;
    }

    html.modal-opened {
      overflow: hidden;
    }

    @media screen and (min-width: 375px) {
      html {
        font-size: 10px;
      }
    }

    @supports (-moz-appearance:none) {
      @media screen and (min-width: 992px) {
        html {
          width: calc(100vw - 8px);
        }
      }
    }

    /* region  fonts */
    @font-face {
      font-family: 'Montserrat';
      src: url(<?= get_template_directory_uri() ?>/theme-fonts/Montserrat-Regular.woff2) format('woff2');
      font-weight: 400;
      font-style: normal;
      font-display: swap;
    }

    @font-face {
      font-family: 'Montserrat';
      src: url(<?= get_template_directory_uri() ?>/theme-fonts/Montserrat-SemiBold.woff2) format('woff2');
      font-weight: 600;
      font-style: normal;
      font-display: swap;
    }

    @font-face {
      font-family: 'Hahmlet';
      src: url(<?= get_template_directory_uri() ?>/theme-fonts/Hahmlet-Regular.woff2) format('woff2');
      font-weight: 400;
      font-style: normal;
      font-display: swap;
    }


    @font-face {
      font-family: 'TitlingGothicFB';
      src: url(<?= get_template_directory_uri() ?>/theme-fonts/TitlingGothicFB-Wide.woff2) format('woff2'),
      url(<?= get_template_directory_uri() ?>/theme-fonts/TitlingGothicFB-Wide.woff) format('woff');
      font-weight: 500;
      font-style: normal;
      font-display: swap;
    }

    /* endregion*/


  </style>
  <!-- Third party code ACF-->
  <?php

  $code_in_head_tag = get_field('code_in_head_tag', 'options');
  $code_before_body_tag_after_head_tag = get_field('code_before_body_tag_after_head_tag', 'options');
  $code_after_body_tag = get_field('code_after_body_tag', 'options');
  ?>
  <?php wp_head(); ?>
  <?= $code_in_head_tag ?>
</head>
<?php flush(); ?>
<?= $code_before_body_tag_after_head_tag ?>
<!--preloader style-->
<style>
  body:not(.loaded) {
    opacity: 0;
  }

  body:not(.loaded) * {
    /*transition: none !important;*/
  }

  body {
    transition: opacity .5s;
  }

  [modal-content] {
    display: none !important;
  }

  .page-transition {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 100;
    pointer-events: none;
    opacity: 0;
    background-color: #EDA677;
    /*clip-path: polygon(0% 0%, 0% 100%, 0% 100%, 0% 0%);*/
  }
</style>
<!--end preloader style-->
<!-- ACF Fields -->
<?php
$warm_red_page = get_field('warm_red_page', get_the_ID());
$warm_red_page = $warm_red_page ?' warm-red-page ' : '';
?>
<!-- END ACF -->
<body <?php body_class($warm_red_page); ?>>
<div class="page-transition"></div>
<a skip-to-main-content href="#main-content"> <?= __('Skip to main content', 'threeSixty_theme') ?></a>
<?= $code_after_body_tag ?>
<!-- remove header if page template if full with no header and footer-->
<main id="main-content" class="theme-wp-site-blocks">
  <header class="tes-header">
    <div class="header-wrapper">
      <!-- burger menu and cross-->
      <button aria-label="Open Menu Links" class="burger-menu">
        <span></span>
        <span></span>
      </button>
      <!--     links  -->
      <nav class="navbar vh">
        <div class="navbar-wrapper">
          <a href="<?= site_url() ?>" class="header-logo cover-image" role="img" aria-labelledby="hero-description">
            <svg width="1379" height="374" viewBox="0 0 1379 374" fill="none" aria-hidden="true">
              <title id="hero-description">Hollywood Hero Background Decorative
                SVG</title>
              <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M1093.25 4.7868C1000.71 18.2312 949.453 66.7558 962.363 128.701C973.694 183.071 1018.38 203.337 1164.35 220.286C1270.27 232.585 1289.86 241.525 1281.72 273.838C1269.16 323.656 1065.14 312.313 1051.83 261.056L1050.37 255.439C1049.92 253.718 1048.38 252.507 1046.61 252.479L960.761 251.127C958.394 251.09 956.528 253.134 956.772 255.495L957.85 265.877C965.466 339.392 1047.34 377.55 1189.29 373.74C1321.75 370.186 1383.2 327.565 1374.09 245.574C1367.33 184.819 1330.02 166.396 1176.46 147.977C1078.32 136.206 1057.6 130.079 1052.95 111.465C1045.85 83.0639 1073.26 71.8104 1150.02 71.6171C1222.59 71.435 1261.91 84.3483 1268.98 110.693L1270.28 115.575C1270.74 117.281 1272.27 118.477 1274.03 118.505L1359.79 119.856C1362.18 119.894 1364.05 117.81 1363.77 115.43L1362.64 105.95C1353.64 30.1793 1232.24 -15.4066 1093.25 4.7868ZM0.939896 75.6198C0.939896 77.806 2.70666 79.5782 4.88607 79.5782H155.81C157.989 79.5782 159.756 81.3504 159.756 83.5366V365.209C159.756 367.394 161.523 369.166 163.702 369.166H247.331C249.51 369.166 251.277 367.394 251.277 365.209V83.5366C251.277 81.3504 253.044 79.5782 255.223 79.5782H406.147C408.326 79.5782 410.092 77.806 410.092 75.6198V11.1396C410.092 8.95347 408.326 7.18126 406.147 7.18126H4.88607C2.70666 7.18126 0.939896 8.95347 0.939896 11.1396V75.6198ZM510.56 365.209C510.56 367.394 512.326 369.166 514.505 369.166H867.315C869.494 369.166 871.26 367.394 871.26 365.209V300.728C871.26 298.543 869.494 296.769 867.315 296.769H600.644C598.462 296.769 596.696 294.997 596.696 292.812V228.361C596.696 226.182 598.451 224.414 600.622 224.402L855.303 223.052C857.443 223.041 859.184 221.322 859.228 219.177L860.565 156.047C860.612 153.837 858.843 152.016 856.639 152.005L601.886 150.655C599.746 150.644 598.007 148.925 597.96 146.781L596.627 83.6206C596.58 81.4021 598.36 79.5782 600.572 79.5782H861.931C864.11 79.5782 865.876 77.806 865.876 75.6198V11.1396C865.876 8.95347 864.11 7.18126 861.931 7.18126H514.505C512.326 7.18126 510.56 8.95347 510.56 11.1396V365.209Z"
                    fill="white"/>
            </svg>
          </a>
          <?php if (have_rows('header_links', 'options')) { ?>
            <div class="row page-links-wrapper">
              <?php
              while (have_rows('header_links', 'options')) {
                the_row();
                $page_link = get_sub_field('link');
                ?>
                <?php if ($page_link) { ?>
                  <div class="col-12 col-md-6 col-lg-6">
                    <a class="page-link animation white-symbol text-uppercase sans-h2" href="<?= $page_link['url'] ?>" target="<?= $page_link['target'] ?>">
                      <span><?= $page_link['title'] ?></span>
                      <svg class="arrow" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                        <path d="M24.9996 8V21C24.9996 21.2652 24.8942 21.5196 24.7067 21.7071C24.5192 21.8946 24.2648 22 23.9996 22C23.7344 22 23.48 21.8946 23.2925 21.7071C23.1049 21.5196 22.9996 21.2652 22.9996 21V10.4137L8.70708 24.7075C8.51944 24.8951 8.26494 25.0006 7.99958 25.0006C7.73422 25.0006 7.47972 24.8951 7.29208 24.7075C7.10444 24.5199 6.99902 24.2654 6.99902 24C6.99902 23.7346 7.10444 23.4801 7.29208 23.2925L21.5858 9H10.9996C10.7344 9 10.48 8.89464 10.2925 8.70711C10.1049 8.51957 9.99958 8.26522 9.99958 8C9.99958 7.73478 10.1049 7.48043 10.2925 7.29289C10.48 7.10536 10.7344 7 10.9996 7H23.9996C24.2648 7 24.5192 7.10536 24.7067 7.29289C24.8942 7.48043 24.9996 7.73478 24.9996 8Z" fill="#F3E9E7"></path>
                      </svg>
                      <svg class="symbol" width="586" height="337" viewBox="0 0 586 337" fill="none" aria-hidden="true">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.48597 148.959C42.3382 89.9755 136.243 0.0117188 293 0.0117188C449.757 0.0117188 543.662 89.9755 580.514 148.959C587.829 160.667 587.829 175.357 580.514 187.064C543.662 246.048 449.757 336.012 293 336.012C136.243 336.012 42.3381 246.048 5.48596 187.064C-1.82866 175.357 -1.82865 160.667 5.48597 148.959ZM304.96 288.014C307.791 288.014 310.086 285.711 310.086 282.871V211.471L360.402 261.959C362.403 263.968 365.649 263.968 367.65 261.959L385.771 243.777C387.773 241.768 387.773 238.512 385.771 236.503L335.454 186.015H406.612C409.443 186.015 411.738 183.712 411.738 180.872V155.158C411.738 152.317 409.443 150.015 406.612 150.015H310.086V53.1568C310.086 50.3165 307.791 48.0139 304.96 48.0139L279.333 48.0139C276.503 48.0139 274.208 50.3165 274.208 53.1568V124.56L223.891 74.0709C221.889 72.0625 218.644 72.0625 216.643 74.0709L198.522 92.2536C196.52 94.262 196.52 97.5183 198.522 99.5267L248.838 150.015H177.68C174.849 150.015 172.554 152.317 172.554 155.158V180.872C172.554 183.712 174.849 186.015 177.68 186.015H248.838L198.522 236.503C196.52 238.511 196.52 241.767 198.522 243.776L216.643 261.958C218.645 263.967 221.89 263.967 223.891 261.958L274.208 211.47V282.871C274.208 285.711 276.503 288.014 279.334 288.014H304.96ZM380.295 79.6002C369.514 68.7823 352.034 68.7823 341.253 79.6002L340.711 80.1438C329.93 90.9617 329.93 108.501 340.711 119.319C351.492 130.137 368.972 130.137 379.753 119.319L380.295 118.775C391.076 107.957 391.076 90.4181 380.295 79.6002Z" fill="white"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M305.406 288C308.237 288 310.532 285.697 310.532 282.857V211.457L360.848 261.945C362.849 263.954 366.094 263.954 368.096 261.945L386.217 243.763C388.218 241.754 388.218 238.498 386.217 236.489L335.9 186.001H407.058C409.889 186.001 412.184 183.698 412.184 180.858V155.144C412.184 152.303 409.889 150.001 407.058 150.001H310.532V53.1429C310.532 50.3025 308.237 48 305.406 48L279.779 48C276.949 48 274.654 50.3025 274.654 53.1429V124.546L224.337 74.057C222.335 72.0485 219.09 72.0485 217.088 74.057L198.968 92.2397C196.966 94.2481 196.966 97.5044 198.968 99.5128L249.284 150.001H178.125C175.295 150.001 173 152.303 173 155.144V180.858C173 183.698 175.295 186.001 178.125 186.001H249.284L198.968 236.489C196.966 238.497 196.966 241.753 198.968 243.762L217.089 261.944C219.09 263.953 222.336 263.953 224.337 261.944L274.654 211.456V282.857C274.654 285.697 276.949 288 279.779 288H305.406ZM380.741 79.5863C369.959 68.7683 352.48 68.7683 341.699 79.5863L341.157 80.1299C330.376 90.9478 330.376 108.487 341.157 119.305C351.938 130.123 369.418 130.123 380.199 119.305L380.741 118.761C391.522 107.944 391.522 90.4042 380.741 79.5863Z" fill="#1A151C"/>
                      </svg>
                    </a>
                  </div>
                <?php } ?>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
      </nav>
    </div>
  </header>



