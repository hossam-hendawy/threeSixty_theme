import gsap from "gsap";

export function accordion(accordions) {
  if (accordions.length === 0) return;
  accordions.forEach((accordion) => {
    const accordionHead = accordion.querySelector('.accordion-head');
    const accordionBody = accordion.querySelector('.accordion-body');
    accordionHead?.addEventListener('click', (e) => {
      if (!accordionBody) {
        return;
      }
      const isOpened = accordion?.classList.toggle('accordion-active');
      if (!isOpened) {
        gsap.to(accordionBody, {height: 0});
      } else {
        gsap.to(Array.from(accordions).map(otherAccordion => {
          const otherAccordionBody = otherAccordion.querySelector('.accordion-body');
          if (otherAccordionBody && accordion !== otherAccordion) {
            otherAccordion?.classList.remove('accordion-active');
            gsap.set(otherAccordion, {zIndex: 1});
          }
          return otherAccordionBody;
        }), {height: 0});
        gsap.set(accordion, {zIndex: 2});
        gsap.to(accordionBody, {height: 'auto'});
      }
    });
  });
}
