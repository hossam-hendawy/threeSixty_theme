import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
import {Swiper} from "swiper";
import {Navigation} from 'swiper/modules';
import {gsap} from "gsap";
import {ScrollTrigger} from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);

/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const testimonialsBlock = async (block) => {

  let testimonialsSwiper = block.querySelector('.testimonials-swiper')

  // add block code here
  const swiper = new Swiper(testimonialsSwiper, {
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
        slidesPerView: 2.39,
        spaceBetween: 30,
      },
    },
    navigation: {
      nextEl: [...block.querySelectorAll(".swiper-button-next")],
      prevEl: [...block.querySelectorAll(".swiper-button-prev")],
    },
  });


  const logo = block.querySelector('svg.logo');

  if (logo) {
    gsap.fromTo(logo,
      {opacity: 0},
      {
        opacity: 1,
        duration: 1,
        ease: "power2.out",
        scrollTrigger: {
          trigger: block,
          start: "top top",
          toggleActions: "play none none none"
        }
      }
    );
  }


// testing the new hidden value
  animations(block);
  imageLazyLoading(block);
};

export default testimonialsBlock;

