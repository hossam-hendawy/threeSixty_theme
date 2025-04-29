import {gsap} from "gsap";
import CSSRulePlugin from "gsap/CSSRulePlugin";
gsap.registerPlugin(CSSRulePlugin);


export  function heroAnimation(container = document) {

  const isolationMode = container.querySelector('.isolation-mode img');
  const afterElement = CSSRulePlugin.getRule(".hero-block:after");
  const tl = gsap.timeline();

  if (afterElement) {
    tl.fromTo(
      afterElement,
      {opacity: 0},
      {
        opacity: 1,
        duration: 2,
        ease: "power2.out",
        delay: 1
      }
    );
  }
  if (isolationMode) {
    tl.fromTo(
      isolationMode,
      {opacity: 0},
      {
        opacity: 1,
        duration: 2.5,
        ease: "power2.out",
      }
    );
  }
}
