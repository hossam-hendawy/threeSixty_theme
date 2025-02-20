import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
import {Swiper} from "swiper";
import {Navigation} from 'swiper/modules';

/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const whatWeAreOfferingBlock = async (block) => {

  // add block code here
  const swiper = new Swiper(block.querySelector('.offering-cards'), {
    slidesPerView: 'auto',
    spaceBetween: 16,
    modules: [Navigation],
    breakpoints: {
      600: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      1280: {
        slidesPerView: 3,
        spaceBetween: 32,
      },
    },
    navigation: {
      nextEl: block.querySelector(".swiper-button-next"),
      prevEl: block.querySelector(".swiper-button-prev"),
    },
  });
// testing the new hidden value

    animations(block);
    imageLazyLoading(block);
};

export default whatWeAreOfferingBlock;

