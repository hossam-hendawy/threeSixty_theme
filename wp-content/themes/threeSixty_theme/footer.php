<?php wp_footer(); ?>
<!--Footer ACF-->
<?php

$first_column = get_field('first_column', 'options');
$second_column = get_field('second_column', 'options');
$third_column = get_field('third_column', 'options');
$fourth_column = get_field('fourth_column', 'options');

$contact_email = get_field('contact_email', 'options');
$contact_number = get_field('contact_number', 'options');
$code_before_end_of_body_tag = get_field('code_before_end_of_body_tag', 'options');
$footer_logo = get_field('footer_logo', 'options');
$footer_text = get_field('footer_text', 'options');
?>
<!--region footer-->
<footer>
  <div class="container">
    <div class="content-wrapper">
      <div class="left-content">
        <a href="<?= site_url() ?>" class="footer-logo" target="_self">
          <svg width="129" height="36" viewBox="0 0 129 36" fill="none" aria-hidden="true">
            <path id="Vector" d="M35.0042 35.3055H21.3342V0.364014H35.0042V6.4028H27.1076V16.0324H35.0042V21.9031H27.1076V29.4258H35.0042V35.3055Z" fill="#2CBEFF"/>
            <path id="Vector_2" d="M0 35.3055H13.67V0.364014H0V6.4028H7.89658V16.0324H0V21.9031H7.89658V29.4258H0V35.3055Z" fill="#2CBEFF"/>
            <path id="Vector_3" d="M127.117 34.09C126.912 34.2984 126.606 34.4027 126.199 34.4027C125.762 34.4027 125.416 34.2766 125.163 34.0244C124.909 33.7722 124.782 33.4191 124.782 32.9652H123.854C123.854 33.4024 123.957 33.7898 124.164 34.1277C124.371 34.4657 124.655 34.7279 125.016 34.9146C125.378 35.1012 125.772 35.1946 126.199 35.1946C126.865 35.1946 127.391 35.0155 127.778 34.6574C128.164 34.2994 128.358 33.8226 128.358 33.2275C128.358 32.6625 128.202 32.2053 127.891 31.8554C127.58 31.5058 127.063 31.2113 126.34 30.9721C125.876 30.807 125.533 30.6202 125.311 30.4112C125.089 30.2024 124.978 29.9431 124.978 29.6332C124.978 29.2559 125.081 28.9628 125.286 28.754C125.491 28.5452 125.78 28.4406 126.154 28.4406C126.564 28.4406 126.878 28.5652 127.097 28.8139C127.315 29.0628 127.425 29.4141 127.425 29.8682H128.353C128.353 29.4579 128.262 29.0815 128.081 28.739C127.899 28.3965 127.64 28.1291 127.304 27.9369C126.967 27.7448 126.584 27.6488 126.154 27.6488C125.525 27.6488 125.016 27.8371 124.628 28.2137C124.24 28.5902 124.045 29.0696 124.045 29.6513C124.045 29.9876 124.113 30.2859 124.25 30.5466C124.386 30.8072 124.591 31.0393 124.865 31.2426C125.139 31.4461 125.535 31.6402 126.053 31.8252C126.571 32.0102 126.929 32.2002 127.127 32.3952C127.326 32.5903 127.425 32.8711 127.425 33.2376C127.425 33.5973 127.322 33.8816 127.117 34.09ZM116.051 27.7497V35.0937H116.974V32.2339L116.888 29.389L118.799 35.0937H119.506L121.427 29.3687L121.342 32.2339V35.0937H122.265V27.7497H121.074L119.158 33.742L117.241 27.7497H116.051ZM114.078 30.9072H111.481V28.5415H114.457V27.7497H110.558V35.0937H114.507V34.3017H111.481V31.699H114.078V30.9072ZM109.11 27.7497H104.328V28.5415H106.255V35.0937H107.178V28.5415H109.11V27.7497ZM102.003 34.09C101.798 34.2984 101.492 34.4027 101.085 34.4027C100.648 34.4027 100.302 34.2766 100.048 34.0244C99.7946 33.7722 99.6676 33.4191 99.6676 32.9652H98.7396C98.7396 33.4024 98.8429 33.7898 99.0497 34.1277C99.2566 34.4657 99.5407 34.7279 99.9023 34.9146C100.264 35.1012 100.658 35.1946 101.085 35.1946C101.751 35.1946 102.277 35.0155 102.664 34.6574C103.05 34.2994 103.244 33.8226 103.244 33.2275C103.244 32.6625 103.088 32.2053 102.777 31.8554C102.466 31.5058 101.949 31.2113 101.226 30.9721C100.762 30.807 100.419 30.6202 100.197 30.4112C99.9753 30.2024 99.8644 29.9431 99.8644 29.6332C99.8644 29.2559 99.9669 28.9628 100.172 28.754C100.377 28.5452 100.666 28.4406 101.04 28.4406C101.45 28.4406 101.764 28.5652 101.983 28.8139C102.201 29.0628 102.311 29.4141 102.311 29.8682H103.239C103.239 29.4579 103.148 29.0815 102.966 28.739C102.785 28.3965 102.526 28.1291 102.19 27.9369C101.853 27.7448 101.47 27.6488 101.04 27.6488C100.411 27.6488 99.9023 27.8371 99.5138 28.2137C99.1255 28.5902 98.9312 29.0696 98.9312 29.6513C98.9312 29.9876 98.9993 30.2859 99.1356 30.5466C99.2717 30.8072 99.4768 31.0393 99.7508 31.2426C100.025 31.4461 100.421 31.6402 100.939 31.8252C101.457 32.0102 101.815 32.2002 102.013 32.3952C102.212 32.5903 102.311 32.8711 102.311 33.2376C102.311 33.5973 102.208 33.8816 102.003 34.09ZM93.8115 27.7497H92.7623L94.8505 32.3547V35.0937H95.7737V32.3547L97.8619 27.7497H96.8178L95.3147 31.4367L93.8115 27.7497ZM90.5884 34.09C90.3832 34.2984 90.0772 34.4027 89.6704 34.4027C89.2332 34.4027 88.8878 34.2766 88.6339 34.0244C88.38 33.7722 88.2531 33.4191 88.2531 32.9652H87.325C87.325 33.4024 87.4283 33.7898 87.6351 34.1277C87.8419 34.4657 88.1262 34.7279 88.4875 34.9146C88.8491 35.1012 89.2433 35.1946 89.6704 35.1946C90.3362 35.1946 90.8624 35.0155 91.2492 34.6574C91.6359 34.2994 91.8293 33.8226 91.8293 33.2275C91.8293 32.6625 91.6736 32.2053 91.3626 31.8554C91.0516 31.5058 90.5345 31.2113 89.8116 30.9721C89.3477 30.807 89.0046 30.6202 88.7826 30.4112C88.5608 30.2024 88.4498 29.9431 88.4498 29.6332C88.4498 29.2559 88.5524 28.9628 88.7575 28.754C88.9625 28.5452 89.2517 28.4406 89.625 28.4406C90.0352 28.4406 90.3495 28.5652 90.5682 28.8139C90.7867 29.0628 90.8961 29.4141 90.8961 29.8682H91.8242C91.8242 29.4579 91.7335 29.0815 91.5518 28.739C91.3703 28.3965 91.1113 28.1291 90.775 27.9369C90.4388 27.7448 90.0554 27.6488 89.625 27.6488C88.9962 27.6488 88.4875 27.8371 88.0992 28.2137C87.7108 28.5902 87.5166 29.0696 87.5166 29.6513C87.5166 29.9876 87.5848 30.2859 87.7208 30.5466C87.8571 30.8072 88.0622 31.0393 88.3363 31.2426C88.6103 31.4461 89.0063 31.6402 89.5242 31.8252C90.042 32.0102 90.4001 32.2002 90.5985 32.3952C90.7968 32.5903 90.8961 32.8711 90.8961 33.2376C90.8961 33.5973 90.7935 33.8816 90.5884 34.09ZM82.5079 34.09C82.3028 34.2984 81.9967 34.4027 81.5899 34.4027C81.1527 34.4027 80.8073 34.2766 80.5534 34.0244C80.2995 33.7722 80.1726 33.4191 80.1726 32.9652H79.2445C79.2445 33.4024 79.3478 33.7898 79.5546 34.1277C79.7614 34.4657 80.0457 34.7279 80.4071 34.9146C80.7686 35.1012 81.1628 35.1946 81.5899 35.1946C82.2557 35.1946 82.7819 35.0155 83.1688 34.6574C83.5554 34.2994 83.7488 33.8226 83.7488 33.2275C83.7488 32.6625 83.5931 32.2053 83.2821 31.8554C82.9711 31.5058 82.4541 31.2113 81.7311 30.9721C81.2672 30.807 80.9241 30.6202 80.7021 30.4112C80.4803 30.2024 80.3693 29.9431 80.3693 29.6332C80.3693 29.2559 80.4719 28.9628 80.677 28.754C80.882 28.5452 81.1712 28.4406 81.5445 28.4406C81.9548 28.4406 82.2691 28.5652 82.4878 28.8139C82.7063 29.0628 82.8156 29.4141 82.8156 29.8682H83.7437C83.7437 29.4579 83.6528 29.0815 83.4713 28.739C83.2898 28.3965 83.0308 28.1291 82.6946 27.9369C82.3583 27.7448 81.9749 27.6488 81.5445 27.6488C80.9157 27.6488 80.4071 27.8371 80.0188 28.2137C79.6303 28.5902 79.4361 29.0696 79.4361 29.6513C79.4361 29.9876 79.5043 30.2859 79.6404 30.5466C79.7766 30.8072 79.9818 31.0393 80.2558 31.2426C80.5298 31.4461 80.9258 31.6402 81.4437 31.8252C81.9615 32.0102 82.3196 32.2002 82.518 32.3952C82.7163 32.5903 82.8156 32.8711 82.8156 33.2376C82.8156 33.5973 82.7131 33.8816 82.5079 34.09ZM76.7931 34.09C76.588 34.2984 76.2819 34.4027 75.8751 34.4027C75.4379 34.4027 75.0925 34.2766 74.8386 34.0244C74.5846 33.7722 74.4577 33.4191 74.4577 32.9652H73.5296C73.5296 33.4024 73.6331 33.7898 73.8399 34.1277C74.0466 34.4657 74.3307 34.7279 74.6923 34.9146C75.0537 35.1012 75.448 35.1946 75.8751 35.1946C76.5409 35.1946 77.0672 35.0155 77.4538 34.6574C77.8404 34.2994 78.0339 33.8226 78.0339 33.2275C78.0339 32.6625 77.8784 32.2053 77.5674 31.8554C77.2563 31.5058 76.7393 31.2113 76.0163 30.9721C75.5522 30.807 75.2093 30.6202 74.9874 30.4112C74.7654 30.2024 74.6544 29.9431 74.6544 29.6332C74.6544 29.2559 74.7569 28.9628 74.9621 28.754C75.1672 28.5452 75.4564 28.4406 75.8297 28.4406C76.24 28.4406 76.5543 28.5652 76.773 28.8139C76.9915 29.0628 77.1008 29.4141 77.1008 29.8682H78.0289C78.0289 29.4579 77.9381 29.0815 77.7565 28.739C77.5749 28.3965 77.3159 28.1291 76.9798 27.9369C76.6435 27.7448 76.2601 27.6488 75.8297 27.6488C75.2009 27.6488 74.6923 27.8371 74.3038 28.2137C73.9155 28.5902 73.7214 29.0696 73.7214 29.6513C73.7214 29.9876 73.7893 30.2859 73.9256 30.5466C74.0618 30.8072 74.2668 31.0393 74.541 31.2426C74.815 31.4461 75.211 31.6402 75.7288 31.8252C76.2466 32.0102 76.6047 32.2002 76.8032 32.3952C77.0016 32.5903 77.1008 32.8711 77.1008 33.2376C77.1008 33.5973 76.9981 33.8816 76.7931 34.09ZM71.9205 30.9072H69.3229V28.5415H72.2989V27.7497H68.3998V35.0937H72.3493V34.3017H69.3229V31.699H71.9205V30.9072ZM65.8526 32.7584C65.8022 33.3771 65.6711 33.8059 65.4592 34.0446C65.2474 34.2834 64.9009 34.4027 64.4201 34.4027C63.9393 34.4027 63.5735 34.2043 63.3232 33.8076C63.0726 33.4108 62.9473 32.8409 62.9473 32.0976V30.7508C62.9473 29.9942 63.0818 29.4201 63.3508 29.0284C63.6199 28.6366 64.0049 28.4406 64.5058 28.4406C64.9397 28.4406 65.2625 28.5685 65.4744 28.824C65.6863 29.0797 65.8123 29.5068 65.8526 30.1052H66.7858C66.7521 29.315 66.5411 28.708 66.1528 28.2844C65.7643 27.8605 65.2154 27.6488 64.5058 27.6488C63.7358 27.6488 63.1281 27.9261 62.6825 28.4811C62.2368 29.0359 62.0141 29.7909 62.0141 30.7459V32.0774C62.0141 33.0357 62.231 33.7949 62.6649 34.3549C63.0986 34.9146 63.6838 35.1946 64.4201 35.1946C65.1531 35.1946 65.7207 34.9853 66.1226 34.5666C66.5243 34.1479 66.7455 33.5453 66.7858 32.7584L65.8526 32.7584ZM59.6334 32.7584C59.583 33.3771 59.4519 33.8059 59.24 34.0446C59.0281 34.2834 58.6817 34.4027 58.2009 34.4027C57.7199 34.4027 57.3543 34.2043 57.1038 33.8076C56.8532 33.4108 56.7281 32.8409 56.7281 32.0976V30.7508C56.7281 29.9942 56.8626 29.4201 57.1316 29.0284C57.4005 28.6366 57.7855 28.4406 58.2866 28.4406C58.7204 28.4406 59.0433 28.5685 59.2552 28.824C59.4669 29.0797 59.5931 29.5068 59.6334 30.1052H60.5666C60.5329 29.315 60.3219 28.708 59.9336 28.2844C59.5451 27.8605 58.9962 27.6488 58.2866 27.6488C57.5166 27.6488 56.9087 27.9261 56.4633 28.4811C56.0176 29.0359 55.7949 29.7909 55.7949 30.7459V32.0774C55.7949 33.0357 56.0118 33.7949 56.4455 34.3549C56.8794 34.9146 57.4644 35.1946 58.2009 35.1946C58.9339 35.1946 59.5013 34.9853 59.9032 34.5666C60.3051 34.1479 60.5261 33.5453 60.5666 32.7584L59.6334 32.7584ZM51.8304 29.0408L52.8341 32.38H50.8316L51.8304 29.0408ZM53.6512 35.0937H54.5994L52.2288 27.7497H51.437L49.0713 35.0937H50.0195L50.5895 33.172H53.0711L53.6512 35.0937Z" fill="white"/>
            <path id="Vector_4" d="M124.252 15.6614C124.19 17.1904 123.931 18.2467 123.477 18.8302C123.022 19.414 122.196 19.7057 120.998 19.7057C119.778 19.7057 118.918 19.2874 118.418 18.4505C117.916 17.6139 117.666 16.0748 117.666 13.8331V9.57166C117.697 7.61936 117.965 6.2169 118.472 5.36466C118.978 4.51242 119.84 4.08638 121.059 4.08638C122.258 4.08638 123.082 4.3886 123.531 4.99285C123.98 5.5971 124.226 6.69495 124.267 8.2857H128.838C128.662 5.71341 127.937 3.7404 126.661 2.36633C125.385 0.992628 123.518 0.30542 121.059 0.30542C118.559 0.30542 116.607 1.14483 115.202 2.8235C113.797 4.50235 113.095 6.89094 113.095 9.99019V13.7866C113.095 16.8961 113.774 19.29 115.133 20.9686C116.491 22.6475 118.446 23.4867 120.998 23.4867C123.425 23.4867 125.305 22.8257 126.638 21.5033C127.97 20.181 128.693 18.2337 128.807 15.6614L124.252 15.6614ZM97.1774 0.615316H81.5424V4.41167H87.0277V23.1768H91.5988V4.41167H97.1774V0.615316ZM79.8359 0.615316H75.2957V15.4289L68.6174 0.615316H64.0616V23.1768H68.6174V8.37856L75.2804 23.1768H79.8359V0.615316ZM61.0692 10.8412H53.9722V4.41167H62.3397V0.615316H49.4165V23.1768H62.3707V19.3958H53.9722V14.5135H61.0692V10.8412Z" fill="white"/>
            <path id="Vector_5" d="M110.544 10.8412H103.447V4.41172H111.814V0.615356H98.8909V23.1768H111.845V19.3959H103.447V14.5135H110.544V10.8412Z" fill="white"/>
          </svg>
        </a>
        <div class="company-info flex-col">
          <?php if ($contact_number) : ?>
            <a href="tel:<?= $contact_number ?>" class="en-h6 white-color contact-number hover-effect">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.58685 5.90211C6.05085 6.86853 6.68337 7.77429 7.48443 8.57534C8.28548 9.37639 9.19124 10.0089 10.1577 10.4729C10.2408 10.5128 10.2823 10.5328 10.3349 10.5481C10.5218 10.6026 10.7513 10.5635 10.9096 10.4501C10.9542 10.4182 10.9923 10.3801 11.0685 10.3039C11.3016 10.0708 11.4181 9.95431 11.5353 9.87812C11.9772 9.59079 12.5469 9.59079 12.9889 9.87812C13.106 9.95431 13.2226 10.0708 13.4556 10.3039L13.5856 10.4338C13.9398 10.7881 14.117 10.9653 14.2132 11.1555C14.4046 11.5339 14.4046 11.9807 14.2132 12.3591C14.117 12.5494 13.9399 12.7265 13.5856 13.0808L13.4805 13.1859C13.1274 13.539 12.9508 13.7155 12.7108 13.8504C12.4445 14 12.0308 14.1075 11.7253 14.1066C11.45 14.1058 11.2619 14.0524 10.8856 13.9456C8.86333 13.3716 6.95509 12.2886 5.36311 10.6967C3.77112 9.10467 2.68814 7.19643 2.11416 5.17417C2.00735 4.79787 1.95395 4.60972 1.95313 4.33442C1.95222 4.02894 2.0598 3.61528 2.20941 3.34894C2.34424 3.10892 2.52078 2.93238 2.87386 2.5793L2.97895 2.47421C3.33325 2.11992 3.5104 1.94277 3.70065 1.84654C4.07903 1.65516 4.52587 1.65516 4.90424 1.84654C5.0945 1.94277 5.27164 2.11991 5.62594 2.47421L5.75585 2.60412C5.98892 2.83719 6.10546 2.95373 6.18165 3.07091C6.46898 3.51284 6.46898 4.08256 6.18165 4.52449C6.10546 4.64167 5.98892 4.75821 5.75585 4.99128C5.67964 5.06749 5.64154 5.10559 5.60965 5.15013C5.4963 5.30842 5.45717 5.53793 5.51165 5.72483C5.52698 5.77742 5.54694 5.81899 5.58685 5.90211Z" stroke="#EAAA08" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <?= $contact_number ?></a>
          <?php endif; ?>
          <?php if ($contact_email) : ?>
            <a href="mailto:<?= $contact_email ?>" class="body white-color hover-effect"><?= $contact_email ?>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.33301 4.66663L6.77629 8.47692C7.21707 8.78547 7.43746 8.93974 7.67718 8.9995C7.88894 9.05228 8.11041 9.05228 8.32217 8.9995C8.56189 8.93974 8.78228 8.78547 9.22306 8.47692L14.6663 4.66663M4.53301 13.3333H11.4663C12.5864 13.3333 13.1465 13.3333 13.5743 13.1153C13.9506 12.9236 14.2566 12.6176 14.4484 12.2413C14.6663 11.8134 14.6663 11.2534 14.6663 10.1333V5.86663C14.6663 4.74652 14.6663 4.18647 14.4484 3.75864C14.2566 3.38232 13.9506 3.07636 13.5743 2.88461C13.1465 2.66663 12.5864 2.66663 11.4663 2.66663H4.53301C3.4129 2.66663 2.85285 2.66663 2.42503 2.88461C2.0487 3.07636 1.74274 3.38232 1.55099 3.75864C1.33301 4.18647 1.33301 4.74652 1.33301 5.86663V10.1333C1.33301 11.2534 1.33301 11.8134 1.55099 12.2413C1.74274 12.6176 2.0487 12.9236 2.42503 13.1153C2.85285 13.3333 3.4129 13.3333 4.53301 13.3333Z" stroke="#EAAA08" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="right-content">
          <?php
          if (have_rows('first_column', 'options')) :
            while (have_rows('first_column', 'options')) :
              the_row();
              ?>
              <?php if (have_rows('footer_link')) : ?>
              <div class="links-wrapper flex-col gab-20">
                <?php while (have_rows('footer_link')) : the_row();
                  $link = get_sub_field('link');
                  ?>
                  <?php if ($link) { ?>
                    <a class="text-sm medium link" href="<?= $link['url'] ?>" target="<?= $link['target'] ?>">
                      <svg class="link-svg" width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 10.5L4.5 6L0 1.5" stroke="#667085" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <?= $link['title'] ?></a>
                  <?php } ?>
                <?php endwhile; ?>
              </div>
            <?php endif; ?>
            <?php endwhile;
          endif; ?>
          <?php
          if (have_rows('second_column', 'options')) :
            while (have_rows('second_column', 'options')) :
              the_row();
              ?>
              <?php if (have_rows('footer_link')) : ?>
              <div class="links-wrapper flex-col gab-20">
                <?php while (have_rows('footer_link')) : the_row();
                  $link = get_sub_field('link');
                  ?>
                  <?php if ($link) { ?>
                    <a class="text-sm medium link" href="<?= $link['url'] ?>" target="<?= $link['target'] ?>">
                      <svg class="link-svg" width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 10.5L4.5 6L0 1.5" stroke="#667085" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <?= $link['title'] ?></a>

                  <?php } ?>
                <?php endwhile; ?>
              </div>
            <?php endif; ?>
            <?php endwhile;
          endif; ?>
          <?php
          if (have_rows('third_column', 'options')) :
            while (have_rows('third_column', 'options')) :
              the_row();
              ?>
              <?php if (have_rows('footer_link')) : ?>
              <div class="links-wrapper flex-col gab-20">
                <?php while (have_rows('footer_link')) : the_row();
                  $link = get_sub_field('link');
                  ?>
                  <?php if ($link) { ?>
                    <a class="text-sm medium link" href="<?= $link['url'] ?>" target="<?= $link['target'] ?>">
                      <svg class="link-svg" width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 10.5L4.5 6L0 1.5" stroke="#667085" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <?= $link['title'] ?></a>

                  <?php } ?>
                <?php endwhile; ?>
              </div>
            <?php endif; ?>
            <?php endwhile;
          endif; ?>

        <?php
          if (have_rows('fourth_column', 'options')) :
            while (have_rows('fourth_column', 'options')) :
              the_row();
              ?>
              <?php if (have_rows('footer_link')) : ?>
              <div class="links-wrapper flex-col gab-20">
                <?php while (have_rows('footer_link')) : the_row();
                  $link = get_sub_field('link');
                  ?>
                  <?php if ($link) { ?>
                    <a class="text-sm medium link" href="<?= $link['url'] ?>" target="<?= $link['target'] ?>">
                      <svg class="link-svg" width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 10.5L4.5 6L0 1.5" stroke="#667085" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <?= $link['title'] ?></a>
                  <?php } ?>
                <?php endwhile; ?>
              </div>
            <?php endif; ?>
            <?php endwhile;
          endif; ?>
      </div>
    </div>
    <div class="social-links-policy">
      <?php if ($footer_text): ?>
        <div class="captions text-md regular white-color"><?= $footer_text ?></div>
      <?php endif; ?>
      <?php if (have_rows('social_links', 'options')) { ?>
          <div class="social-links-wrapper">
            <?php while (have_rows('social_links', 'options')) {
              the_row();
              $url = get_sub_field('url');
              $icon = get_sub_field('icon');
              ?>
              <a href="<?= $url ?>" target="_blank" class="social-link">
                <?php if (!empty($icon) && is_array($icon)) { ?>
                  <picture class="icon-wrapper cover-image">
                    <img src="<?= $icon['url'] ?>" alt="<?= $icon['alt'] ?>">
                  </picture>
                <?php } ?>
              </a>
            <?php } ?>
          </div>
        <?php } ?>
    </div>
  </div>
</footer>
</main>
<?= $code_before_end_of_body_tag ?>
</body>
</html>
