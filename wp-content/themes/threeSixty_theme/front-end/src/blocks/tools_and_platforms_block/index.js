import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
import {autoplaySwiper} from "../../scripts/general/autoplay-swiper.js"

/**
 * @author HOSSAM
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const toolsAndPlatformsBlock = async (block) => {

  const container = block.querySelector(".autoplay-swiper");
  container?.addEventListener("mouseover", () => {
    container.style.animationPlayState = "paused";
  });
  container?.addEventListener("mouseout", () => {
    container.style.animationPlayState = "running";
  });

  autoplaySwiper(block, {
    slidesPerView: 4,
    spaceBetween: 20,
    speed: 20000,
    breakpoints: {
      600: {
        slidesPerView: 5,
        spaceBetween: 32
      },
      768: {
        slidesPerView: 7,
        spaceBetween: 32
      },
    }
  });

  animations(block);
  imageLazyLoading(block);
};

export default toolsAndPlatformsBlock;

