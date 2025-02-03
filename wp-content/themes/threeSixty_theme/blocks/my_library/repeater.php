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



<!-- swiper -->
<?php if (have_rows('advisory_team_members')) { ?>
  <div class="swiper-wrapper">
    <?php while (have_rows('advisory_team_members')) {
      the_row();
      $link = get_sub_field('url');
      $image = get_sub_field('image');
      $name = get_sub_field('name');
      $job_title = get_sub_field('job_title');
      $description = get_sub_field('description');
      ?>
      <div class="swiper-slide">
        <?php if ($link) { ?>
          <a class="card has-link" href="<?= $link ?>" target="_blank" aria-label="(opens in a new tab)">
            <picture class="image-wrapper">
              <?= \Theme\Helpers::get_image($image, 'large'); ?>
            </picture>
            <div class="arrow-wrapper">
              <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                <path d="M28 13C28 13.2652 27.8946 13.5196 27.7071 13.7071C27.5196 13.8946 27.2652 14 27 14C26.7348 14 26.4804 13.8946 26.2929 13.7071C26.1054 13.5196 26 13.2652 26 13V7.415L17.7087 15.7075C17.5211 15.8951 17.2666 16.0006 17.0012 16.0006C16.7359 16.0006 16.4814 15.8951 16.2938 15.7075C16.1061 15.5199 16.0007 15.2654 16.0007 15C16.0007 14.7346 16.1061 14.4801 16.2938 14.2925L24.585 6H19C18.7348 6 18.4804 5.89464 18.2929 5.70711C18.1054 5.51957 18 5.26522 18 5C18 4.73478 18.1054 4.48043 18.2929 4.29289C18.4804 4.10536 18.7348 4 19 4H27C27.2652 4 27.5196 4.10536 27.7071 4.29289C27.8946 4.48043 28 4.73478 28 5V13ZM23 16C22.7348 16 22.4804 16.1054 22.2929 16.2929C22.1054 16.4804 22 16.7348 22 17V26H6V10H15C15.2652 10 15.5196 9.89464 15.7071 9.70711C15.8946 9.51957 16 9.26522 16 9C16 8.73478 15.8946 8.48043 15.7071 8.29289C15.5196 8.10536 15.2652 8 15 8H6C5.46957 8 4.96086 8.21071 4.58579 8.58579C4.21071 8.96086 4 9.46957 4 10V26C4 26.5304 4.21071 27.0391 4.58579 27.4142C4.96086 27.7893 5.46957 28 6 28H22C22.5304 28 23.0391 27.7893 23.4142 27.4142C23.7893 27.0391 24 26.5304 24 26V17C24 16.7348 23.8946 16.4804 23.7071 16.2929C23.5196 16.1054 23.2652 16 23 16Z"
                      fill="#F3E9E7"/>
              </svg>
            </div>
            <span class="name-and-job">
                    <?php if ($name) { ?>
                      <h3 class="sans-h3 card-title text-uppercase off-white-color"><?= $name ?></h3>
                    <?php } ?>
              <?php if ($job_title) { ?>
                <h3 class="body card-job text-uppercase  gray-color"><?= $job_title ?></h3>
              <?php } ?>
                </span>
          </a>
        <?php } else { ?>
          <div class="card">
            <picture class="image-wrapper">
              <?= \Theme\Helpers::get_image($image, 'large'); ?>
            </picture>
            <div class="arrow-wrapper"></div>
            <span class="name-and-job">
                    <?php if ($name) { ?>
                      <h3 class="sans-h3 card-title text-uppercase off-white-color"><?= $name ?></h3>
                    <?php } ?>
              <?php if ($job_title) { ?>
                <h3 class="body card-job text-uppercase  gray-color"><?= $job_title ?></h3>
              <?php } ?>
                    </span>
          </div>
        <?php } ?>
        <?php if ($description) { ?>
          <div class="body card-description off-white-color gray-color"> <?= $description ?></div>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
<?php } ?>
