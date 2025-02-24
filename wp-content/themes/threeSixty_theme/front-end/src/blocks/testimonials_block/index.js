import './style.scss';
import { imageLazyLoading } from "../../scripts/functions/imageLazyLoading";
import { animations } from "../../scripts/general/animations";
import { Swiper } from "swiper";
import { Navigation } from 'swiper/modules';

/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const testimonialsBlock = async (block) => {

  const swiperButtonsNext = block.querySelectorAll('.swiper-button-next');
  const swiperButtonsPrev = block.querySelectorAll('.swiper-button-prev');

  console.log(swiperButtonsNext)
  console.log(swiperButtonsPrev)
  const swiper = new Swiper(block.querySelector('.testimonials-swiper'), {
    slidesPerView: 1,
    spaceBetween: 16,
    modules: [Navigation],
    breakpoints: {
      600: {
        spaceBetween: 20,
        slidesPerView: 2,
      },
      768: {
        slidesPerView: 2,
      },
      992: {
        slidesPerView: 3,
      },
      1280: {
        slidesPerView: 2.26,
        spaceBetween: 30,
      },
    },
    navigation: {
      nextEl: swiperButtonsNext.length ? swiperButtonsNext : null,
      prevEl: swiperButtonsPrev.length ? swiperButtonsPrev : null,
    },
  });

  animations(block);
  imageLazyLoading(block);
};

export default testimonialsBlock;
