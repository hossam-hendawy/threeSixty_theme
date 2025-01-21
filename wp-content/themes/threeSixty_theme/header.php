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
      font-family: 'Inter';
      src: url(<?= get_template_directory_uri() ?>/theme-fonts/Inter/Inter_24pt-Regular.woff2) format('woff2');
      font-weight: 400;
      font-style: normal;
      font-display: swap;
    }

    @font-face {
      font-family: 'Inter';
      src: url(<?= get_template_directory_uri() ?>/theme-fonts/Inter/Inter_24pt-Medium.woff2) format('woff2');
      font-weight: 500;
      font-style: normal;
      font-display: swap;
    }


    @font-face {
      font-family: 'Inter';
      src: url(<?= get_template_directory_uri() ?>/theme-fonts/Inter/Inter_24pt-SemiBold.woff2) format('woff2');
      font-weight: 600;
      font-style: normal;
      font-display: swap;
    }

    @font-face {
      font-family: 'Inter';
      src: url(<?= get_template_directory_uri() ?>/theme-fonts/Inter/Inter_24pt-Bold.woff2) format('woff2');
      font-weight: 700;
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

    </div>
  </header>



