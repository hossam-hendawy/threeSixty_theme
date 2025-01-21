import Swiper from 'swiper';
import {Navigation} from 'swiper/modules';

export function handelSwiper(block) {

  let swiper = new Swiper(block, {
    slidesPerView: 'auto',
    spaceBetween: 24,
    modules: [Navigation],
    navigation: {
      nextEl: block.querySelector(".swiper-button-next"),
      prevEl: block.querySelector(".swiper-button-prev"),
    },
    breakpoints: {
      600: {
        slidesPerView: 'auto',
      },
      700: {
        slidesPerView: 'auto',
      },
      1024: {
        slidesPerView: 2.04,
      },
      1280: {
        slidesPerView: 2.41,
      },
      1440: {
        slidesPerView: 2.655,
      },
      1920: {
        slidesPerView: 3.39,
      },
    },
  });

}
