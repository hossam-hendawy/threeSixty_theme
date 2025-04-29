import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
import {gsap} from "gsap";
import {ScrollTrigger} from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);

/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */

const whoWeAreBlock = async (block) => {

  const logo = block.querySelector('svg.logo');

  if (logo) {
    gsap.fromTo(logo,
      {opacity: 0},
      {
        opacity: 1,
        duration: 1.5,
        ease: "power2.out",
        scrollTrigger: {
          trigger: block,
          start: "top 80%",
          toggleActions: "play none none none"
        }
      }
    );
  }

  animations(block);
  imageLazyLoading(block);
};

export default whoWeAreBlock;
