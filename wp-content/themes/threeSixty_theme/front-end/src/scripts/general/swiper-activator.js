/**
 * activates and destroys swiper according to the current media
 * @param activeMedia {string}
 * @param onActive {function}
 * @param onDestroy {function}
 */
export function swiperActivator(activeMedia, onActive, onDestroy) {
  const breakpoint = window.matchMedia(activeMedia);
  let isActive = false;
  const breakpointChecker = () => {
    if (breakpoint.matches && !isActive) {
      isActive = true;
      onActive?.();
    } else if (!breakpoint.matches && isActive) {
      isActive = false;
      onDestroy?.();
    }
  };

  breakpoint.addEventListener('change', breakpointChecker);

  breakpointChecker();
}

// region how to use
// let swiper;
// swiperActivator('(min-width:991px)', () => {
//   swiper = new Swiper(block.querySelector('.product-wrapper'), {
//     pagination: {
//       el: block.querySelector('.product-sidebar-wrapper .swiper-pagination'),
//       clickable: true,
//     },
//     slidesPerView: 2,
//     spaceBetween: 20,
//     observer: true,
//     observeParents: true
//   });
// }, () => swiper?.destroy?.())
// endregion how to use


