import { gsap } from "gsap";
import CSSRulePlugin from "gsap/CSSRulePlugin";
gsap.registerPlugin(CSSRulePlugin);

export function heroAnimation(block, selector = ".hero-block:after") {
  const isolationMode = block.querySelector('.isolation-mode img');
  const afterElement = CSSRulePlugin.getRule(selector);
  const tl = gsap.timeline();

  if (afterElement) {
    tl.fromTo(
      afterElement,
      { opacity: 0 },
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
      { opacity: 0 },
      {
        opacity: 1,
        duration: 1,
        ease: "power2.out",
      }
    );
  }
}
