<?php wp_footer(); ?>
<!--Footer ACF-->
<?php


$code_before_end_of_body_tag = get_field('code_before_end_of_body_tag', 'options');
$footer_logo = get_field('footer_logo', 'options');
?>
<!--region footer-->
<footer>
  <picture class="footer-bg cover-image">
    <img src="<?= get_template_directory_uri() . '/images/backgrounds/footer-bg.png' ?>" alt="A city skyline at night with tall buildings silhouetted against a dark, cloudy sky, and scattered lights glowing from the windows">
  </picture>
  <div class="full-container">
    <div class="row page-links-wrapper">
      <?php if (have_rows('page_links', 'options')) { ?>
        <?php while (have_rows('page_links', 'options')) {
          the_row();
          ?>
          <?php if (have_rows('links', 'options')) { ?>
            <?php while (have_rows('links', 'options')) {
              the_row();
              $menu_link = get_sub_field('link');
              ?>
              <?php if (\Theme\Helpers::get_key_from_array('title', $menu_link)) { ?>
                <div class="col-12 col-tablet-6 col-md-6 col-lg-6">
                  <a class="page-link text-uppercase sans-h2" href="<?= $menu_link['url'] ?>" target="<?= $menu_link['target'] ?>">
                    <span><?= $menu_link['title'] ?></span>
                    <svg class="arrow" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                      <path d="M24.9996 8V21C24.9996 21.2652 24.8942 21.5196 24.7067 21.7071C24.5192 21.8946 24.2648 22 23.9996 22C23.7344 22 23.48 21.8946 23.2925 21.7071C23.1049 21.5196 22.9996 21.2652 22.9996 21V10.4137L8.70708 24.7075C8.51944 24.8951 8.26494 25.0006 7.99958 25.0006C7.73422 25.0006 7.47972 24.8951 7.29208 24.7075C7.10444 24.5199 6.99902 24.2654 6.99902 24C6.99902 23.7346 7.10444 23.4801 7.29208 23.2925L21.5858 9H10.9996C10.7344 9 10.48 8.89464 10.2925 8.70711C10.1049 8.51957 9.99958 8.26522 9.99958 8C9.99958 7.73478 10.1049 7.48043 10.2925 7.29289C10.48 7.10536 10.7344 7 10.9996 7H23.9996C24.2648 7 24.5192 7.10536 24.7067 7.29289C24.8942 7.48043 24.9996 7.73478 24.9996 8Z" fill="#F3E9E7"/>
                    </svg>
                    <svg class="symbol" width="586" height="337" viewBox="0 0 586 337" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M5.48597 148.959C42.3382 89.9755 136.243 0.0117188 293 0.0117188C449.757 0.0117188 543.662 89.9755 580.514 148.959C587.829 160.667 587.829 175.357 580.514 187.064C543.662 246.048 449.757 336.012 293 336.012C136.243 336.012 42.3381 246.048 5.48596 187.064C-1.82866 175.357 -1.82865 160.667 5.48597 148.959ZM304.96 288.014C307.791 288.014 310.086 285.711 310.086 282.871V211.471L360.402 261.959C362.403 263.968 365.649 263.968 367.65 261.959L385.771 243.777C387.773 241.768 387.773 238.512 385.771 236.503L335.454 186.015H406.612C409.443 186.015 411.738 183.712 411.738 180.872V155.158C411.738 152.317 409.443 150.015 406.612 150.015H310.086V53.1568C310.086 50.3165 307.791 48.0139 304.96 48.0139L279.333 48.0139C276.503 48.0139 274.208 50.3165 274.208 53.1568V124.56L223.891 74.0709C221.889 72.0625 218.644 72.0625 216.643 74.0709L198.522 92.2536C196.52 94.262 196.52 97.5183 198.522 99.5267L248.838 150.015H177.68C174.849 150.015 172.554 152.317 172.554 155.158V180.872C172.554 183.712 174.849 186.015 177.68 186.015H248.838L198.522 236.503C196.52 238.511 196.52 241.767 198.522 243.776L216.643 261.958C218.645 263.967 221.89 263.967 223.891 261.958L274.208 211.47V282.871C274.208 285.711 276.503 288.014 279.334 288.014H304.96ZM380.295 79.6002C369.514 68.7823 352.034 68.7823 341.253 79.6002L340.711 80.1438C329.93 90.9617 329.93 108.501 340.711 119.319C351.492 130.137 368.972 130.137 379.753 119.319L380.295 118.775C391.076 107.957 391.076 90.4181 380.295 79.6002Z" fill="#F84E35"/>
                    </svg>
                    <svg class="symbol in-warm-page" width="586" height="337" viewBox="0 0 586 337" fill="none" aria-hidden="true">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M5.48597 148.959C42.3382 89.9755 136.243 0.0117188 293 0.0117188C449.757 0.0117188 543.662 89.9755 580.514 148.959C587.829 160.667 587.829 175.357 580.514 187.064C543.662 246.048 449.757 336.012 293 336.012C136.243 336.012 42.3381 246.048 5.48596 187.064C-1.82866 175.357 -1.82865 160.667 5.48597 148.959ZM304.96 288.014C307.791 288.014 310.086 285.711 310.086 282.871V211.471L360.402 261.959C362.403 263.968 365.649 263.968 367.65 261.959L385.771 243.777C387.773 241.768 387.773 238.512 385.771 236.503L335.454 186.015H406.612C409.443 186.015 411.738 183.712 411.738 180.872V155.158C411.738 152.317 409.443 150.015 406.612 150.015H310.086V53.1568C310.086 50.3165 307.791 48.0139 304.96 48.0139L279.333 48.0139C276.503 48.0139 274.208 50.3165 274.208 53.1568V124.56L223.891 74.0709C221.889 72.0625 218.644 72.0625 216.643 74.0709L198.522 92.2536C196.52 94.262 196.52 97.5183 198.522 99.5267L248.838 150.015H177.68C174.849 150.015 172.554 152.317 172.554 155.158V180.872C172.554 183.712 174.849 186.015 177.68 186.015H248.838L198.522 236.503C196.52 238.511 196.52 241.767 198.522 243.776L216.643 261.958C218.645 263.967 221.89 263.967 223.891 261.958L274.208 211.47V282.871C274.208 285.711 276.503 288.014 279.334 288.014H304.96ZM380.295 79.6002C369.514 68.7823 352.034 68.7823 341.253 79.6002L340.711 80.1438C329.93 90.9617 329.93 108.501 340.711 119.319C351.492 130.137 368.972 130.137 379.753 119.319L380.295 118.775C391.076 107.957 391.076 90.4181 380.295 79.6002Z" fill="white"/>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M305.406 288C308.237 288 310.532 285.697 310.532 282.857V211.457L360.848 261.945C362.849 263.954 366.094 263.954 368.096 261.945L386.217 243.763C388.218 241.754 388.218 238.498 386.217 236.489L335.9 186.001H407.058C409.889 186.001 412.184 183.698 412.184 180.858V155.144C412.184 152.303 409.889 150.001 407.058 150.001H310.532V53.1429C310.532 50.3025 308.237 48 305.406 48L279.779 48C276.949 48 274.654 50.3025 274.654 53.1429V124.546L224.337 74.057C222.335 72.0485 219.09 72.0485 217.088 74.057L198.968 92.2397C196.966 94.2481 196.966 97.5044 198.968 99.5128L249.284 150.001H178.125C175.295 150.001 173 152.303 173 155.144V180.858C173 183.698 175.295 186.001 178.125 186.001H249.284L198.968 236.489C196.966 238.497 196.966 241.753 198.968 243.762L217.089 261.944C219.09 263.953 222.336 263.953 224.337 261.944L274.654 211.456V282.857C274.654 285.697 276.949 288 279.779 288H305.406ZM380.741 79.5863C369.959 68.7683 352.48 68.7683 341.699 79.5863L341.157 80.1299C330.376 90.9478 330.376 108.487 341.157 119.305C351.938 130.123 369.418 130.123 380.199 119.305L380.741 118.761C391.522 107.944 391.522 90.4042 380.741 79.5863Z" fill="#1A151C"/>
                    </svg>
                  </a>
                </div>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        <?php } ?>
      <?php } ?>
    </div>
    <div class="row">
      <div class="col-12 col-tablet-6 col-md-6 col-lg-6">
        <?php if ($footer_logo) { ?>
          <a class="footer-logo" href="<?= site_url() ?>" aria-label="Go to homepage">
            <?= \Theme\Helpers::get_image($footer_logo, 'large'); ?>
          </a>
        <?php } ?>
      </div>
      <div class="col-12 col-tablet-6  col-md-6 col-lg-6">
        <div class="left-content-wrapper">
          <div class="footer-links">
            <?php if (have_rows('footer_links', 'options')) { ?>
              <?php while (have_rows('footer_links', 'options')) {
                the_row();
                ?>
                <?php if (have_rows('column', 'options')) { ?>
                  <?php while (have_rows('column', 'options')) {
                    the_row();
                    $title = get_sub_field('title');
                    ?>
                    <div class="bottom-links">
                      <div class="title subheading off-white-color"><?= $title ?></div>
                      <?php if (have_rows('links', 'options')) { ?>
                        <?php while (have_rows('links', 'options')) {
                          the_row();
                          $menu_link = get_sub_field('link');
                          ?>
                          <?php if (\Theme\Helpers::get_key_from_array('title', $menu_link)) {
                            $aria_label = $menu_link['target'] === '_blank' ? 'aria-label="' . $menu_link['title'] . ' (opens in a new tab)"' : '';
                            ?>
                            <a class="small-copy off-white-color" <?= $aria_label ?> href="<?= $menu_link['url'] ?>" target="<?= $menu_link['target'] ?>"><?= $menu_link['title'] ?> </a>
                          <?php } ?>
                        <?php } ?>
                      <?php } ?>
                    </div>
                  <?php } ?>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          </div>
          <div class="copy-right">
            <p class="small-copy off-white-color"> ©<?= date('Y') ?> All rights
              reserved</p>
            <a class="small-copy off-white-color " href="https://huntandhawk.com/" target="_blank" aria-label="Go to Hunt and Hawk homepage">
              Crafted by Hunt + Hawk</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>
</main>
<?= $code_before_end_of_body_tag ?>
</body>
</html>
