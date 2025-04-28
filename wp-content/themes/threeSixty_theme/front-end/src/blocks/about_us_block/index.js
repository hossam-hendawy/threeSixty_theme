import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
import {gsap} from "gsap"; // تأكد ان gsap متضاف عندك
import CSSRulePlugin from "gsap/CSSRulePlugin";
gsap.registerPlugin(CSSRulePlugin);

/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const aboutUsBlock = async (block) => {

  const isolationMode = block.querySelector('.isolation-mode img');
  const afterElement = CSSRulePlugin.getRule(".about_us_block:after");
  const tl = gsap.timeline();

  if (afterElement) {
    tl.fromTo(
      afterElement,
      {opacity: 0},
      {
        opacity: 1,
        duration: 1.5,
        ease: "power2.out",
        delay: 0.5
      }
    );
  }
  if (isolationMode) {
    tl.fromTo(
      isolationMode,
      {opacity: 0},
      {
        opacity: 1,
        duration: 1.5,
        ease: "power2.out",
      }
    );
  }

  animations(block);
  imageLazyLoading(block);
};

export default aboutUsBlock;

