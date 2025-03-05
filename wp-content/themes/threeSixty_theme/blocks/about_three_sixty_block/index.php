<?php
// @author DELL
// Create id attribute allowing for custom "anchor" value.
$id = '';
$className = $dataClass = 'about_three_sixty_block';
if (isset($block)) {
  $id = 'block_' . uniqid();
  if (!empty($block['anchor'])) {
    $id = $block['anchor'];
  }

// Create class attribute allowing for custom "className" and "align" values.
  if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
  }
  if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
  }
  if (get_field('is_screenshot')) :
    /* Render screenshot for example */
    echo '<img width="100%" height="100%" src="' . get_template_directory_uri() . '/blocks/about_three_sixty_block/screenshot.png" >';

    return;
  endif;
}
/****************************
 *     Custom ACF Meta      *
 ****************************/
$sub_title = get_field('sub_title');
$title = get_field('title');
$description = get_field('description');
$image = get_field('image');
?>
<!-- region threeSixty_theme's Block -->
<?php general_settings_for_blocks($id, $className, $dataClass); ?>
<div class="container">
  <div class="cards-wrapper">
  <div class="left-content flex-col gab-20">
    <?php if ($sub_title): ?>
      <h2 class="text-xl sub-title"><?= $sub_title ?></h2>
    <?php endif; ?>
    <?php if ($title): ?>
      <h3 class="bold title"><?= $title ?></h3>
    <?php endif; ?>
    <?php if ($description): ?>
      <div class="text-lg description"><?= $description ?></div>
    <?php endif; ?>
  </div>
    <?php if (!empty($image) && is_array($image)) { ?>
    <div class="right-image">
      <picture class="image image-wrapper cover-image ">
        <img src="<?= $image['url'] ?>" alt="<?= $image['alt'] ?>">
      </picture>
  </div>
    <?php } ?>
  </div>


  <div class="content-wrapper">

    <div class="left-content">
      <h4 class="text-xl text-uppercase white-color bold left-content-title">Our Services</h4>
      <div class="package-wrapper">
        <div class="package-box">
            <picture class="package-icon cover-image">
              <svg xmlns="http://www.w3.org/2000/svg" width="33" height="37" viewBox="0 0 33 37" fill="none">
                <path d="M22 37H1L11.4975 18.491H11.5025L22 37Z" fill="#3F621A"/>
                <path d="M21.9975 37L11.5 18.491H32.5" fill="#669F2A"/>
                <path d="M22 0L11.5025 18.509H11.4975L1 0" fill="#669F2A"/>
                <path d="M32.5 18.509H11.5L21.9975 0" fill="#669F2A"/>
              </svg>
            </picture>
            <div class="title-and-excerpt">
              <div class="package-title text-xl white-color">SEO Optimization</div>
              <div class="package-description text-sm">Full SEO and content marketing for large businesses.</div>
            </div>
        </div>
        <div class="package-box">
          <picture class="package-icon cover-image">
            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="37" viewBox="0 0 33 37" fill="none">
              <path d="M22 37H1L11.4975 18.491H11.5025L22 37Z" fill="#3F621A"/>
              <path d="M21.9975 37L11.5 18.491H32.5" fill="#669F2A"/>
              <path d="M22 0L11.5025 18.509H11.4975L1 0" fill="#669F2A"/>
              <path d="M32.5 18.509H11.5L21.9975 0" fill="#669F2A"/>
            </svg>
          </picture>
          <div class="title-and-excerpt">
            <div class="package-title text-xl white-color">SEO Optimization</div>
            <div class="package-description text-sm">Full SEO and content marketing for large businesses.</div>
          </div>
        </div>
        <div class="package-box">
          <picture class="package-icon cover-image">
            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="37" viewBox="0 0 33 37" fill="none">
              <path d="M22 37H1L11.4975 18.491H11.5025L22 37Z" fill="#3F621A"/>
              <path d="M21.9975 37L11.5 18.491H32.5" fill="#669F2A"/>
              <path d="M22 0L11.5025 18.509H11.4975L1 0" fill="#669F2A"/>
              <path d="M32.5 18.509H11.5L21.9975 0" fill="#669F2A"/>
            </svg>
          </picture>
          <div class="title-and-excerpt">
            <div class="package-title text-xl white-color">SEO Optimization</div>
            <div class="package-description text-sm">Full SEO and content marketing for large businesses.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="right-content flex-col">
      <div class="info-box-card">
      <h5 class="text-xl text-uppercase white-color bold right-content-title">Help & Support</h5>
      <div class="info-box-wrapper">
        <div class="info-box">
        <picture class="info-box-image cover-image">
          <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.5 9.00224C10.6762 8.50136 11.024 8.079 11.4817 7.80998C11.9395 7.54095 12.4777 7.4426 13.001 7.53237C13.5243 7.62213 13.999 7.89421 14.3409 8.30041C14.6829 8.70661 14.87 9.22072 14.8692 9.75168C14.8692 11.2506 12.6209 12 12.6209 12M12.6499 15H12.6599M10.4 19.7L11.86 21.6467C12.0771 21.9362 12.1857 22.0809 12.3188 22.1327C12.4353 22.178 12.5647 22.178 12.6812 22.1327C12.8143 22.0809 12.9229 21.9362 13.14 21.6467L14.6 19.7C14.8931 19.3091 15.0397 19.1137 15.2185 18.9645C15.4569 18.7656 15.7383 18.6248 16.0405 18.5535C16.2671 18.5 16.5114 18.5 17 18.5C18.3978 18.5 19.0967 18.5 19.6481 18.2716C20.3831 17.9672 20.9672 17.3831 21.2716 16.6481C21.5 16.0967 21.5 15.3978 21.5 14V8.3C21.5 6.61984 21.5 5.77976 21.173 5.13803C20.8854 4.57354 20.4265 4.1146 19.862 3.82698C19.2202 3.5 18.3802 3.5 16.7 3.5H8.3C6.61984 3.5 5.77976 3.5 5.13803 3.82698C4.57354 4.1146 4.1146 4.57354 3.82698 5.13803C3.5 5.77976 3.5 6.61984 3.5 8.3V14C3.5 15.3978 3.5 16.0967 3.72836 16.6481C4.03284 17.3831 4.61687 17.9672 5.35195 18.2716C5.90326 18.5 6.60218 18.5 8 18.5C8.48858 18.5 8.73287 18.5 8.95951 18.5535C9.26169 18.6248 9.54312 18.7656 9.7815 18.9645C9.96028 19.1137 10.1069 19.3091 10.4 19.7Z" stroke="#98A2B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </picture>
        <div class="package-title text-md semi-bold white-color">SEO Optimization

      </div>
      </div>
        <div class="info-box">
          <picture class="info-box-image cover-image">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M10.5 9.00224C10.6762 8.50136 11.024 8.079 11.4817 7.80998C11.9395 7.54095 12.4777 7.4426 13.001 7.53237C13.5243 7.62213 13.999 7.89421 14.3409 8.30041C14.6829 8.70661 14.87 9.22072 14.8692 9.75168C14.8692 11.2506 12.6209 12 12.6209 12M12.6499 15H12.6599M10.4 19.7L11.86 21.6467C12.0771 21.9362 12.1857 22.0809 12.3188 22.1327C12.4353 22.178 12.5647 22.178 12.6812 22.1327C12.8143 22.0809 12.9229 21.9362 13.14 21.6467L14.6 19.7C14.8931 19.3091 15.0397 19.1137 15.2185 18.9645C15.4569 18.7656 15.7383 18.6248 16.0405 18.5535C16.2671 18.5 16.5114 18.5 17 18.5C18.3978 18.5 19.0967 18.5 19.6481 18.2716C20.3831 17.9672 20.9672 17.3831 21.2716 16.6481C21.5 16.0967 21.5 15.3978 21.5 14V8.3C21.5 6.61984 21.5 5.77976 21.173 5.13803C20.8854 4.57354 20.4265 4.1146 19.862 3.82698C19.2202 3.5 18.3802 3.5 16.7 3.5H8.3C6.61984 3.5 5.77976 3.5 5.13803 3.82698C4.57354 4.1146 4.1146 4.57354 3.82698 5.13803C3.5 5.77976 3.5 6.61984 3.5 8.3V14C3.5 15.3978 3.5 16.0967 3.72836 16.6481C4.03284 17.3831 4.61687 17.9672 5.35195 18.2716C5.90326 18.5 6.60218 18.5 8 18.5C8.48858 18.5 8.73287 18.5 8.95951 18.5535C9.26169 18.6248 9.54312 18.7656 9.7815 18.9645C9.96028 19.1137 10.1069 19.3091 10.4 19.7Z" stroke="#98A2B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </picture>
          <div class="package-title text-md semi-bold white-color">SEO Optimization

          </div>
        </div>
        <div class="info-box">
          <picture class="info-box-image cover-image">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M10.5 9.00224C10.6762 8.50136 11.024 8.079 11.4817 7.80998C11.9395 7.54095 12.4777 7.4426 13.001 7.53237C13.5243 7.62213 13.999 7.89421 14.3409 8.30041C14.6829 8.70661 14.87 9.22072 14.8692 9.75168C14.8692 11.2506 12.6209 12 12.6209 12M12.6499 15H12.6599M10.4 19.7L11.86 21.6467C12.0771 21.9362 12.1857 22.0809 12.3188 22.1327C12.4353 22.178 12.5647 22.178 12.6812 22.1327C12.8143 22.0809 12.9229 21.9362 13.14 21.6467L14.6 19.7C14.8931 19.3091 15.0397 19.1137 15.2185 18.9645C15.4569 18.7656 15.7383 18.6248 16.0405 18.5535C16.2671 18.5 16.5114 18.5 17 18.5C18.3978 18.5 19.0967 18.5 19.6481 18.2716C20.3831 17.9672 20.9672 17.3831 21.2716 16.6481C21.5 16.0967 21.5 15.3978 21.5 14V8.3C21.5 6.61984 21.5 5.77976 21.173 5.13803C20.8854 4.57354 20.4265 4.1146 19.862 3.82698C19.2202 3.5 18.3802 3.5 16.7 3.5H8.3C6.61984 3.5 5.77976 3.5 5.13803 3.82698C4.57354 4.1146 4.1146 4.57354 3.82698 5.13803C3.5 5.77976 3.5 6.61984 3.5 8.3V14C3.5 15.3978 3.5 16.0967 3.72836 16.6481C4.03284 17.3831 4.61687 17.9672 5.35195 18.2716C5.90326 18.5 6.60218 18.5 8 18.5C8.48858 18.5 8.73287 18.5 8.95951 18.5535C9.26169 18.6248 9.54312 18.7656 9.7815 18.9645C9.96028 19.1137 10.1069 19.3091 10.4 19.7Z" stroke="#98A2B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </picture>
          <div class="package-title text-md semi-bold white-color">SEO Optimization

          </div>
        </div>
      </div>
      </div>
      <div class="info-box-card">
        <h5 class="text-xl text-uppercase white-color bold right-content-title">Help & Support</h5>
        <div class="info-box-wrapper">
          <div class="info-box">
            <picture class="info-box-image cover-image">
              <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.5 9.00224C10.6762 8.50136 11.024 8.079 11.4817 7.80998C11.9395 7.54095 12.4777 7.4426 13.001 7.53237C13.5243 7.62213 13.999 7.89421 14.3409 8.30041C14.6829 8.70661 14.87 9.22072 14.8692 9.75168C14.8692 11.2506 12.6209 12 12.6209 12M12.6499 15H12.6599M10.4 19.7L11.86 21.6467C12.0771 21.9362 12.1857 22.0809 12.3188 22.1327C12.4353 22.178 12.5647 22.178 12.6812 22.1327C12.8143 22.0809 12.9229 21.9362 13.14 21.6467L14.6 19.7C14.8931 19.3091 15.0397 19.1137 15.2185 18.9645C15.4569 18.7656 15.7383 18.6248 16.0405 18.5535C16.2671 18.5 16.5114 18.5 17 18.5C18.3978 18.5 19.0967 18.5 19.6481 18.2716C20.3831 17.9672 20.9672 17.3831 21.2716 16.6481C21.5 16.0967 21.5 15.3978 21.5 14V8.3C21.5 6.61984 21.5 5.77976 21.173 5.13803C20.8854 4.57354 20.4265 4.1146 19.862 3.82698C19.2202 3.5 18.3802 3.5 16.7 3.5H8.3C6.61984 3.5 5.77976 3.5 5.13803 3.82698C4.57354 4.1146 4.1146 4.57354 3.82698 5.13803C3.5 5.77976 3.5 6.61984 3.5 8.3V14C3.5 15.3978 3.5 16.0967 3.72836 16.6481C4.03284 17.3831 4.61687 17.9672 5.35195 18.2716C5.90326 18.5 6.60218 18.5 8 18.5C8.48858 18.5 8.73287 18.5 8.95951 18.5535C9.26169 18.6248 9.54312 18.7656 9.7815 18.9645C9.96028 19.1137 10.1069 19.3091 10.4 19.7Z" stroke="#98A2B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </picture>
            <div class="package-title text-md semi-bold white-color">SEO Optimization

            </div>
          </div>
          <div class="info-box">
            <picture class="info-box-image cover-image">
              <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.5 9.00224C10.6762 8.50136 11.024 8.079 11.4817 7.80998C11.9395 7.54095 12.4777 7.4426 13.001 7.53237C13.5243 7.62213 13.999 7.89421 14.3409 8.30041C14.6829 8.70661 14.87 9.22072 14.8692 9.75168C14.8692 11.2506 12.6209 12 12.6209 12M12.6499 15H12.6599M10.4 19.7L11.86 21.6467C12.0771 21.9362 12.1857 22.0809 12.3188 22.1327C12.4353 22.178 12.5647 22.178 12.6812 22.1327C12.8143 22.0809 12.9229 21.9362 13.14 21.6467L14.6 19.7C14.8931 19.3091 15.0397 19.1137 15.2185 18.9645C15.4569 18.7656 15.7383 18.6248 16.0405 18.5535C16.2671 18.5 16.5114 18.5 17 18.5C18.3978 18.5 19.0967 18.5 19.6481 18.2716C20.3831 17.9672 20.9672 17.3831 21.2716 16.6481C21.5 16.0967 21.5 15.3978 21.5 14V8.3C21.5 6.61984 21.5 5.77976 21.173 5.13803C20.8854 4.57354 20.4265 4.1146 19.862 3.82698C19.2202 3.5 18.3802 3.5 16.7 3.5H8.3C6.61984 3.5 5.77976 3.5 5.13803 3.82698C4.57354 4.1146 4.1146 4.57354 3.82698 5.13803C3.5 5.77976 3.5 6.61984 3.5 8.3V14C3.5 15.3978 3.5 16.0967 3.72836 16.6481C4.03284 17.3831 4.61687 17.9672 5.35195 18.2716C5.90326 18.5 6.60218 18.5 8 18.5C8.48858 18.5 8.73287 18.5 8.95951 18.5535C9.26169 18.6248 9.54312 18.7656 9.7815 18.9645C9.96028 19.1137 10.1069 19.3091 10.4 19.7Z" stroke="#98A2B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </picture>
            <div class="package-title text-md semi-bold white-color">SEO Optimization

            </div>
          </div>
          <div class="info-box">
            <picture class="info-box-image cover-image">
              <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.5 9.00224C10.6762 8.50136 11.024 8.079 11.4817 7.80998C11.9395 7.54095 12.4777 7.4426 13.001 7.53237C13.5243 7.62213 13.999 7.89421 14.3409 8.30041C14.6829 8.70661 14.87 9.22072 14.8692 9.75168C14.8692 11.2506 12.6209 12 12.6209 12M12.6499 15H12.6599M10.4 19.7L11.86 21.6467C12.0771 21.9362 12.1857 22.0809 12.3188 22.1327C12.4353 22.178 12.5647 22.178 12.6812 22.1327C12.8143 22.0809 12.9229 21.9362 13.14 21.6467L14.6 19.7C14.8931 19.3091 15.0397 19.1137 15.2185 18.9645C15.4569 18.7656 15.7383 18.6248 16.0405 18.5535C16.2671 18.5 16.5114 18.5 17 18.5C18.3978 18.5 19.0967 18.5 19.6481 18.2716C20.3831 17.9672 20.9672 17.3831 21.2716 16.6481C21.5 16.0967 21.5 15.3978 21.5 14V8.3C21.5 6.61984 21.5 5.77976 21.173 5.13803C20.8854 4.57354 20.4265 4.1146 19.862 3.82698C19.2202 3.5 18.3802 3.5 16.7 3.5H8.3C6.61984 3.5 5.77976 3.5 5.13803 3.82698C4.57354 4.1146 4.1146 4.57354 3.82698 5.13803C3.5 5.77976 3.5 6.61984 3.5 8.3V14C3.5 15.3978 3.5 16.0967 3.72836 16.6481C4.03284 17.3831 4.61687 17.9672 5.35195 18.2716C5.90326 18.5 6.60218 18.5 8 18.5C8.48858 18.5 8.73287 18.5 8.95951 18.5535C9.26169 18.6248 9.54312 18.7656 9.7815 18.9645C9.96028 19.1137 10.1069 19.3091 10.4 19.7Z" stroke="#98A2B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </picture>
            <div class="package-title text-md semi-bold white-color">SEO Optimization

            </div>
          </div>
        </div>
      </div>
    </div>




</div>
  </div>
</section>


<!-- endregion threeSixty_theme's Block -->
